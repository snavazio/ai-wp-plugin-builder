/**
 * Ingest an existing plugin (from a .zip) and update it to a change request, then re-verify against all
 * 8 gates and re-package at a bumped version. Works with either engine:
 *   - claude: the Agent SDK coder edits the files in place (best for editing existing code).
 *   - local:  any chat platform (engines/llm.ts; default local Ollama) returns modified files via the
 *             ===FILE=== protocol.
 *
 * CLI:  tsx src/run.ts ingest <plugin.zip> <changeRequestFile> [--engine claude|local] [--model X] [--kind fix|change|refactor|update]
 */
import { readFile, writeFile, mkdir, rm, cp, readdir, access } from 'node:fs/promises';
import { join, relative, basename } from 'node:path';
import type { BuildEnv } from './build.js';
import { runAgent, digestFailures, runAudit, blockingFindings, MAX_AUDIT_ROUNDS } from './build.js';
import { loadRules, coder } from './agents.js';
import { makeCoderHooks, newHookStats, type HookStats } from './hooks.js';
import { runPipeline } from './pipeline.js';
import { renderTerminal, renderMarkdown } from './report.js';
import { WpEnv, dockerAvailable } from './wpEnv.js';
import { exec, findPhpFiles } from './util/exec.js';
import { emitResult } from './resultFile.js';
import { ollamaConfig, type OllamaConfig } from './engines/ollama.js';
import { llmChat, platformFromEnv, testPlatform, type ChatMessage } from './engines/llm.js';
import { loadOrBuildIndex, retrieve, formatContext } from './rag.js';
import { parseFiles, writeGeneratedFiles } from './fileProtocol.js';
import { formatFixes } from './fixKb.js';
import type { GateResult, PipelineResult } from './types.js';
import { withSpecCoverage } from './specCoverage.js';
import { emptyResult, finalize } from './gates/_util.js';
import { EDIT_KINDS, type EditKind } from './registry.js';

const MAX_FIX_ITERATIONS = 6;

async function exists(p: string): Promise<boolean> {
  try {
    await access(p);
    return true;
  } catch {
    return false;
  }
}

function bumpPatch(v: string): string {
  const m = v.match(/^(\d+)\.(\d+)(?:\.(\d+))?$/);
  if (!m) return v;
  return `${m[1]}.${m[2]}.${parseInt(m[3] ?? '0', 10) + 1}`;
}

async function walkDirs(dir: string, out: string[] = []): Promise<string[]> {
  out.push(dir);
  for (const e of await readdir(dir, { withFileTypes: true })) {
    if (e.isDirectory() && !['node_modules', 'vendor', '.git'].includes(e.name)) {
      await walkDirs(join(dir, e.name), out);
    }
  }
  return out;
}

/** Find the plugin root inside an extracted zip: the shallowest dir with a *.php file that has a header. */
export async function findPluginRoot(extractDir: string): Promise<{ dir: string; mainFile: string; version: string; name: string } | null> {
  const dirs = (await walkDirs(extractDir)).sort((a, b) => a.length - b.length);
  for (const d of dirs) {
    for (const e of await readdir(d, { withFileTypes: true })) {
      if (e.isFile() && e.name.endsWith('.php')) {
        const src = await readFile(join(d, e.name), 'utf8').catch(() => '');
        if (/^[ \t/*#@]*Plugin Name\s*:/im.test(src)) {
          const version = src.match(/^[ \t/*#@]*Version\s*:\s*(.+)$/im)?.[1]?.trim() ?? '1.0.0';
          const name = src.match(/^[ \t/*#@]*Plugin Name\s*:\s*(.+)$/im)?.[1]?.trim() ?? basename(d);
          return { dir: d, mainFile: join(d, e.name), version, name };
        }
      }
    }
  }
  return null;
}

/** Scan a plugin dir and describe what it exposes + where to find it. */
async function scanProvides(pluginDir: string): Promise<string> {
  let all = '';
  for (const f of await findPhpFiles(pluginDir)) {
    all += '\n' + (await readFile(f, 'utf8').catch(() => ''));
  }
  const parts: string[] = [];
  const shortcodes = [...all.matchAll(/add_shortcode\(\s*['"]([a-z0-9_-]+)['"]/gi)].map((m) => `[${m[1]}]`);
  if (shortcodes.length) parts.push('Shortcode: ' + [...new Set(shortcodes)].join(', '));
  if (/register_post_type\s*\(/.test(all)) parts.push('Custom post type (adds a menu)');
  if (/add_menu_page\s*\(|add_submenu_page\s*\(|add_options_page\s*\(/.test(all)) parts.push('Admin page');
  if (/register_rest_route\s*\(/.test(all)) parts.push('REST endpoint');
  if (/register_block_type\s*\(/.test(all)) parts.push('Block (in the editor)');
  if (/register_widget\s*\(/.test(all)) parts.push('Widget (Appearance → Widgets)');
  if (/wp_schedule_event\s*\(/.test(all)) parts.push('Scheduled task');
  const hasMenu = /register_post_type\s*\(|add_menu_page\s*\(|add_submenu_page\s*\(|add_options_page\s*\(/.test(all);
  if (!hasMenu && parts.length) parts.push('No admin menu — under Plugins → Installed Plugins.');
  return parts.join(' · ');
}

async function snapshotFiles(pluginDir: string): Promise<string> {
  const parts: string[] = [];
  for (const f of await findPhpFiles(pluginDir)) {
    parts.push(`===FILE: ${relative(pluginDir, f)}===\n${await readFile(f, 'utf8')}\n===ENDFILE===`);
  }
  for (const extra of ['readme.txt']) {
    const p = join(pluginDir, extra);
    if (await exists(p)) parts.push(`===FILE: ${extra}===\n${await readFile(p, 'utf8')}\n===ENDFILE===`);
  }
  return parts.join('\n\n');
}

/** Bump the minor version: 1.2.3 -> 1.3.0. */
function bumpMinor(v: string): string {
  const m = v.match(/^(\d+)\.(\d+)(?:\.(\d+))?$/);
  if (!m) return v;
  return `${m[1]}.${parseInt(m[2], 10) + 1}.0`;
}

function headerVersionOf(src: string): string | null {
  return src.match(/^[ \t/*#@]*Version\s*:\s*(.+)$/im)?.[1]?.trim() ?? null;
}

/** Per-kind instruction for the coder. */
function kindInstruction(kind: EditKind, request: string, patchV: string, minorV: string): string {
  const bump = (v: string) =>
    `Bump the version to ${v} in the plugin header, the readme.txt "Stable tag" (if present), and any *_VERSION ` +
    `constant, and add a "= ${v} =" changelog entry to readme.txt if it has a changelog.`;
  switch (kind) {
    case 'fix':
      return (
        `BUG FIX. Fix ONLY this problem; do not add features or refactor unrelated code:\n\n"${request}"\n\n` + bump(patchV)
      );
    case 'change':
      return (
        `CHANGE / NEW FEATURE. Implement this change, keeping all existing behaviour unless the request says ` +
        `otherwise:\n\n"${request}"\n\nUpdate tests/test-smoke.php for anything new. ` + bump(minorV)
      );
    case 'refactor':
      return (
        `REFACTOR ONLY — no behaviour change. Improve the code quality as described below, but do NOT change any ` +
        `output, hooks, option or meta names, database schema, REST routes, shortcodes, or public function/class ` +
        `names. Do NOT edit or delete existing test files (you may add new ones).\n\n"${request}"\n\n` + bump(patchV)
      );
    case 'update':
      return (
        `UPDATE. Apply this request, preserving all existing functionality unless it says otherwise:\n\n"${request}"\n\n` +
        `Decide which it is: if it only fixes a problem, use version ${patchV}; if it adds or changes features or ` +
        `behaviour, use version ${minorV}. Use exactly one of those two. Bump the version in the plugin header, the ` +
        `readme.txt "Stable tag" (if present), and any *_VERSION constant, and add a matching changelog entry.`
      );
  }
}

/** Snapshot of the existing test files, so a refactor can be held to "tests unchanged". */
async function snapshotTests(pluginDir: string): Promise<Map<string, string>> {
  const out = new Map<string, string>();
  for (const f of await findPhpFiles(join(pluginDir, 'tests')).catch(() => [] as string[])) {
    out.set(relative(pluginDir, f), await readFile(f, 'utf8'));
  }
  return out;
}

export async function runIngest(args: string[], env: BuildEnv): Promise<number> {
  // parse
  let engine: 'claude' | 'local' = 'claude';
  let model = '';
  let kind: EditKind = 'update';
  const positional: string[] = [];
  for (let i = 0; i < args.length; i++) {
    if (args[i] === '--engine') engine = args[++i] === 'local' ? 'local' : 'claude';
    else if (args[i] === '--model') model = args[++i] ?? '';
    else if (args[i] === '--kind') {
      const k = args[++i] ?? '';
      if (!(EDIT_KINDS as readonly string[]).includes(k)) {
        console.error(`--kind must be one of: ${EDIT_KINDS.join(', ')}`);
        return 2;
      }
      kind = k as EditKind;
    } else positional.push(args[i]);
  }
  const zipPath = positional[0];
  const changeFile = positional[1];
  if (!zipPath || !changeFile) {
    console.error('Usage: tsx src/run.ts ingest <plugin.zip> <changeRequestFile> [--engine claude|local] [--kind fix|change|refactor|update]');
    return 2;
  }
  const changeRequest = (await readFile(changeFile, 'utf8')).trim();
  const { repoRoot, harnessDir } = env;

  if (!(await dockerAvailable())) {
    console.error('Docker/wp-env unavailable — cannot verify. Aborting.');
    await emitResult({ ok: false, engine, error: 'Docker/wp-env unavailable on the builder service — nothing was changed.' });
    return 2;
  }
  if (engine === 'claude' && !process.env.ANTHROPIC_API_KEY) {
    console.error('ANTHROPIC_API_KEY not set (required for the Claude engine).');
    await emitResult({ ok: false, engine, error: 'No Anthropic API key for the Claude engine — nothing was changed.' });
    return 2;
  }

  // 1. extract
  console.log('\n▶ Extracting plugin…');
  const extractDir = join(repoRoot, 'build', `.ingest-${Date.now()}`);
  await rm(extractDir, { recursive: true, force: true });
  await mkdir(extractDir, { recursive: true });
  const ex = await exec('php', [join(harnessDir, 'bin', 'unzip.php'), zipPath, extractDir], { timeoutMs: 60_000 });
  if (ex.code !== 0) {
    console.error('Could not extract the zip:', ex.stderr || ex.stdout);
    await emitResult({ ok: false, engine, error: 'Could not extract the plugin .zip.' });
    return 1;
  }
  const root = await findPluginRoot(extractDir);
  if (!root) {
    console.error('No WordPress plugin (a PHP file with a "Plugin Name:" header) found in the zip.');
    await emitResult({ ok: false, engine, error: 'No WordPress plugin found in the .zip.' });
    return 1;
  }
  const slug = basename(root.dir);
  const mainRel = relative(root.dir, root.mainFile);
  const patchV = bumpPatch(root.version);
  const minorV = bumpMinor(root.version);
  const allowed = kind === 'change' ? [minorV] : kind === 'update' ? [patchV, minorV] : [patchV];
  console.log(`  Plugin: ${root.name} (slug: ${slug}, ${root.version} → ${allowed.join(' or ')}, kind: ${kind})`);

  // 2. copy into the working dir
  const pluginDir = join(repoRoot, 'build', slug);
  await rm(pluginDir, { recursive: true, force: true });
  await cp(root.dir, pluginDir, { recursive: true });
  await rm(extractDir, { recursive: true, force: true });
  const testsBefore = kind === 'refactor' ? await snapshotTests(pluginDir) : new Map<string, string>();

  // 3. engine setup
  const rules = await loadRules(repoRoot);
  const wpEnv = new WpEnv(repoRoot, pluginDir, slug, harnessDir);
  const hookStats: HookStats = newHookStats();
  const coderHooks = makeCoderHooks(pluginDir, join(repoRoot, 'dist'), hookStats);
  const startTs = Date.now();
  let totalCost = 0;

  const cfg: OllamaConfig = ollamaConfig(); // embeddings for RAG
  const llm = platformFromEnv();
  if (model) llm.model = model;
  if (engine === 'local') {
    const h = await testPlatform(llm);
    console.log(`  Engine: ${llm.protocol} (${llm.model}) @ ${llm.baseUrl}`);
    if (!h.ok) {
      console.error('✖ ' + h.message);
      await emitResult({ ok: false, engine, slug, pluginName: root.name, error: `AI platform unavailable: ${h.message}` });
      return 2;
    }
  }
  // RAG is optional: without an Ollama for embeddings, edit with the full rules only.
  const idx = engine === 'local' ? await loadOrBuildIndex(cfg, repoRoot).catch(() => null) : null;
  const rag = async (q: string, k: number): Promise<string> =>
    idx ? formatContext(await retrieve(idx, cfg, q, k).catch(() => [])) : '';

  /** Extra checks on top of the gates: spec coverage, the version bump for this kind, and (refactor) tests unchanged. */
  const editChecks = async (): Promise<GateResult> => {
    const start = Date.now();
    const r = emptyResult('editRules', `Edit rules (${kind})`, false);
    const mainSrc = await readFile(join(pluginDir, mainRel), 'utf8').catch(() => '');
    const v = headerVersionOf(mainSrc);
    if (!v || !allowed.includes(v)) {
      r.errors.push(`Plugin header Version is "${v ?? 'missing'}"; for a ${kind} it must be ${allowed.join(' or ')} (bump header, readme Stable tag and *_VERSION together).`);
    }
    if (kind === 'refactor') {
      for (const [rel, before] of testsBefore) {
        const now = await readFile(join(pluginDir, rel), 'utf8').catch(() => null);
        if (now === null) r.errors.push(`Refactor deleted the existing test file ${rel}; restore it unchanged.`);
        else if (now !== before) r.errors.push(`Refactor changed the existing test file ${rel}; restore it unchanged (a refactor must pass the old tests).`);
      }
    }
    return finalize(r, start);
  };

  const verify = async (): Promise<PipelineResult> => {
    const pipe = await withSpecCoverage(
      await runPipeline(pluginDir, {
        repoRoot,
        harnessDir,
        wpEnv,
        onGate: (r) => process.stdout.write(`    [${r.skipped ? 'SKIP' : r.passed ? 'PASS' : 'FAIL'}] ${r.label}\n`),
      }),
      pluginDir,
    );
    const er = await editChecks();
    process.stdout.write(`    [${er.passed ? 'PASS' : 'FAIL'}] ${er.label}\n`);
    return { ...pipe, results: [...pipe.results, er], passed: pipe.passed && er.passed };
  };

  /** Apply an instruction with the selected engine. Returns false if the Claude coder crashed twice. */
  const applyChange = async (instruction: string, pipe: PipelineResult | null): Promise<boolean> => {
    if (engine === 'claude') {
      for (let attempt = 1; attempt <= 2; attempt++) {
        const r = await runAgent({ repoRoot, role: coder, rulesAppend: rules, prompt: instruction, hooks: coderHooks, maxTurns: 120, maxBudgetUsd: 5 });
        totalCost += r.costUsd;
        console.log(`  coder done (${r.turns} turns, $${r.costUsd.toFixed(4)}).`);
        if (!r.isError) return true;
        console.log(`  coder ended early${attempt < 2 ? ' — retrying' : ''}: ${r.errorInfo}`);
      }
      return false;
    }
    // local engine — file protocol
    const failing = pipe ? pipe.results.filter((x) => !x.passed && !x.skipped) : [];
    const fixCtx = failing.length
      ? formatFixes(failing.flatMap((x) => [...x.errors, ...x.notes])) + '\n\n' + (await rag('fix ' + failing.map((x) => x.label).join(' '), 4))
      : await rag(changeRequest + ' WordPress plugin security', 5);
    const snap = await snapshotFiles(pluginDir);
    const sys = `You are an expert WordPress developer editing an EXISTING plugin. Keep existing behavior unless asked to change it. Follow these hard rules:\n\n${rules}\n\nOUTPUT PROTOCOL: for every file you create or modify, output the ENTIRE file between markers:\n===FILE: relative/path.php===\n<content>\n===ENDFILE===\nNo prose, no markdown fences.`;
    const msgs: ChatMessage[] = [
      { role: 'system', content: sys },
      { role: 'user', content: `${instruction}\n\nRELEVANT RULES / FIXES:\n${fixCtx}\n\nCURRENT FILES:\n${snap}` },
    ];
    const out = await llmChat(llm, msgs, { temperature: 0.1, numCtx: 32768, timeoutMs: 15 * 60_000 });
    const wr = await writeGeneratedFiles(parseFiles(out.content), pluginDir);
    console.log(`  wrote ${wr.written.length} file(s).`);
    await exec(join(harnessDir, 'vendor', 'bin', 'phpcbf'), ['-q', `--standard=${join(harnessDir, 'phpcs.xml.dist')}`, pluginDir], { timeoutMs: 120_000 });
    return true;
  };

  // The edit/verify/package body runs inside a try so that ANY unexpected throw still produces a report and
  // a result.json (otherwise the service reports the opaque "produced no result").
  let pipe: PipelineResult | null = null;
  let iterations = 0;
  let zipOut: string | null = null;
  let finalVersion = '';
  let crashError: string | null = null;
  let auditFindings: number | undefined;

  try {
    // 4. apply the change
    console.log(`\n▶ ${kind} with ${engine === 'claude' ? 'Claude' : 'the selected model'}…`);
    const ok = await applyChange(
      `You are editing the EXISTING WordPress plugin in build/${slug}/.\n\n` +
        kindInstruction(kind, changeRequest, patchV, minorV) +
        `\n\nAlso keep the plugin fully compliant with the hard rules so it passes all gates (ABSPATH guards, ` +
        `escaped output, sanitized input, nonce AND current_user_can() on every state change, $wpdb->prepare()). ` +
        `When done, stop.`,
      null,
    );
    if (!ok) throw new Error('the coder failed twice before making the change, so nothing was edited');

    // 5. verify → fix loop
    console.log('\n▶ Verifying…');
    pipe = await verify();
    while (!pipe.passed && iterations < MAX_FIX_ITERATIONS) {
      iterations++;
      console.log(`\n  Failed — fix iteration ${iterations}/${MAX_FIX_ITERATIONS}.`);
      await applyChange(`The verification harness FAILED. Fix ONLY these issues in build/${slug}/, then stop.\n\n${digestFailures(pipe)}`, pipe);
      console.log('\n▶ Re-verifying…');
      pipe = await verify();
    }

    // 6. independent auditor (Claude engine; the local engine has no auditor, same as build-local)
    if (pipe.passed && engine === 'claude') {
      console.log('\n▶ security-auditor (independent, read-only)…');
      const first = await runAudit(repoRoot, rules, `Audit the plugin in build/${slug}/ against the hard security rules. Output ONLY the findings JSON block.`);
      totalCost += first.costUsd;
      let findings = first.findings;
      console.log(`  auditor: ${findings.length} finding(s).`);
      let rounds = 0;
      while (blockingFindings(findings).length > 0 && rounds < MAX_AUDIT_ROUNDS) {
        rounds++;
        const fixList = blockingFindings(findings).map((f) => `- [${f.severity}] ${f.file ?? '?'}:${f.line ?? '?'} — ${f.issue} → ${f.required_fix}`).join('\n');
        await applyChange(`The independent security auditor found issues. Fix ONLY these in build/${slug}/, then stop:\n${fixList}`, pipe);
        pipe = await verify();
        if (!pipe.passed) break;
        const again = await runAudit(repoRoot, rules, `Re-audit build/${slug}/ after fixes. Output ONLY the findings JSON block.`);
        totalCost += again.costUsd;
        findings = again.findings;
      }
      auditFindings = findings.length;
      if (pipe.passed && blockingFindings(findings).length > 0) {
        throw new Error(`the security auditor still reports ${blockingFindings(findings).length} medium/high finding(s)`);
      }
    }

    // 7. package if green
    if (pipe.passed) {
      finalVersion = headerVersionOf(await readFile(join(pluginDir, mainRel), 'utf8')) ?? patchV;
      await mkdir(join(repoRoot, 'dist'), { recursive: true });
      zipOut = join(repoRoot, 'dist', `${slug}.${finalVersion}.zip`);
      const pkg = await exec('php', [join(harnessDir, 'bin', 'package.php'), pluginDir, slug, zipOut], { timeoutMs: 60_000 });
      if (pkg.code === 0) console.log('\n▶ Packaged: ' + pkg.stdout.trim());
      else zipOut = null;
    } else {
      console.log('\n' + renderTerminal(pipe));
    }
  } catch (e) {
    crashError = e instanceof Error ? e.message : String(e);
    console.error(`\n✖ Edit run stopped: ${crashError}`);
  }

  const passed = !!pipe?.passed && !!zipOut && !crashError;
  const durMin = ((Date.now() - startTs) / 60000).toFixed(1);
  const outcome = passed ? '✅ edited & verified' : crashError ? `❌ stopped: ${crashError}` : '❌ did not pass all checks';
  const reportPath = join(repoRoot, 'dist', `${slug}-update-report.md`);

  try {
    const report = [
      `# ${kind[0].toUpperCase() + kind.slice(1)} report: ${root.name}`,
      '',
      `- **Slug:** \`${slug}\`  ·  **Version:** ${root.version} → ${finalVersion || allowed.join(' or ')}  ·  **Engine:** ${engine}`,
      `- **Request:** ${changeRequest}`,
      `- **Outcome:** ${outcome}  ·  ${iterations} fix iteration(s)  ·  ${durMin} min  ·  $${totalCost.toFixed(4)}`,
      auditFindings !== undefined ? `- **Auditor findings remaining:** ${auditFindings}` : '- **Auditor:** not run (local engine)',
      '',
      pipe ? renderMarkdown(pipe) : '_No verification results — the run stopped before verifying._',
    ].join('\n');
    await mkdir(join(repoRoot, 'dist'), { recursive: true });
    await writeFile(reportPath, report + '\n', 'utf8');
  } catch (e) {
    console.error('Could not write the report:', e instanceof Error ? e.message : String(e));
  }

  await emitResult({
    ok: passed,
    engine,
    slug,
    pluginName: root.name,
    version: finalVersion || undefined,
    zip: zipOut,
    report: reportPath,
    iterations,
    costUsd: totalCost,
    auditFindings,
    provides: await scanProvides(pluginDir).catch(() => ''),
    error: passed ? undefined : crashError ? crashError : 'did not pass all checks',
  });

  console.log('\n' + '='.repeat(64));
  console.log(`${passed ? '✔' : '✖'} ${kind.toUpperCase()}: ${root.name} ${root.version} → ${finalVersion || '(no new version)'}`);
  if (zipOut) console.log(`   .zip: ${zipOut}`);
  console.log('='.repeat(64));
  return passed ? 0 : 1;
}
