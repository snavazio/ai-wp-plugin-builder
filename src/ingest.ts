/**
 * Ingest an existing plugin (from a .zip) and update it to a change request, then re-verify against all
 * 8 gates and re-package at a bumped version. Works with either engine:
 *   - claude: the Agent SDK coder edits the files in place (best for editing existing code).
 *   - local:  a local Ollama model returns modified files via the ===FILE=== protocol.
 *
 * CLI:  tsx src/run.ts ingest <plugin.zip> <changeRequestFile> [--engine claude|local] [--model X]
 */
import { readFile, writeFile, mkdir, rm, cp, readdir, access } from 'node:fs/promises';
import { join, relative, basename } from 'node:path';
import type { BuildEnv } from './build.js';
import { runAgent, digestFailures } from './build.js';
import { loadRules, coder } from './agents.js';
import { makeCoderHooks, newHookStats, type HookStats } from './hooks.js';
import { runPipeline } from './pipeline.js';
import { renderTerminal, renderMarkdown } from './report.js';
import { WpEnv, dockerAvailable } from './wpEnv.js';
import { exec, findPhpFiles } from './util/exec.js';
import { emitResult } from './resultFile.js';
import { ollamaConfig, ollamaHealth, ollamaChat, type OllamaConfig, type ChatMessage } from './engines/ollama.js';
import { loadOrBuildIndex, retrieve, formatContext } from './rag.js';
import { parseFiles, writeGeneratedFiles } from './fileProtocol.js';
import { formatFixes } from './fixKb.js';
import type { PipelineResult } from './types.js';

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
async function findPluginRoot(extractDir: string): Promise<{ dir: string; mainFile: string; version: string; name: string } | null> {
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

export async function runIngest(args: string[], env: BuildEnv): Promise<number> {
  // parse
  let engine: 'claude' | 'local' = 'claude';
  let model = '';
  const positional: string[] = [];
  for (let i = 0; i < args.length; i++) {
    if (args[i] === '--engine') engine = args[++i] === 'local' ? 'local' : 'claude';
    else if (args[i] === '--model') model = args[++i] ?? '';
    else positional.push(args[i]);
  }
  const zipPath = positional[0];
  const changeFile = positional[1];
  if (!zipPath || !changeFile) {
    console.error('Usage: tsx src/run.ts ingest <plugin.zip> <changeRequestFile> [--engine claude|local]');
    return 2;
  }
  const changeRequest = (await readFile(changeFile, 'utf8')).trim();
  const { repoRoot, harnessDir } = env;

  if (!(await dockerAvailable())) {
    console.error('Docker/wp-env unavailable — cannot verify. Aborting.');
    return 2;
  }
  if (engine === 'claude' && !process.env.ANTHROPIC_API_KEY) {
    console.error('ANTHROPIC_API_KEY not set (required for the Claude engine).');
    return 2;
  }

  // 1. extract
  console.log('\n▶ Extracting uploaded plugin…');
  const extractDir = join(repoRoot, 'build', `.ingest-${Date.now()}`);
  await rm(extractDir, { recursive: true, force: true });
  await mkdir(extractDir, { recursive: true });
  const ex = await exec('php', [join(harnessDir, 'bin', 'unzip.php'), zipPath, extractDir], { timeoutMs: 60_000 });
  if (ex.code !== 0) {
    console.error('Could not extract the zip:', ex.stderr || ex.stdout);
    return 1;
  }
  const root = await findPluginRoot(extractDir);
  if (!root) {
    console.error('No WordPress plugin (a PHP file with a "Plugin Name:" header) found in the zip.');
    return 1;
  }
  const slug = basename(root.dir);
  const newVersion = bumpPatch(root.version);
  console.log(`  Plugin: ${root.name} (slug: ${slug}, ${root.version} → ${newVersion})`);

  // 2. copy into the working dir
  const pluginDir = join(repoRoot, 'build', slug);
  await rm(pluginDir, { recursive: true, force: true });
  await cp(root.dir, pluginDir, { recursive: true });
  await rm(extractDir, { recursive: true, force: true });

  // 3. engine setup
  const rules = await loadRules(repoRoot);
  const wpEnv = new WpEnv(repoRoot, pluginDir, slug, harnessDir);
  const hookStats: HookStats = newHookStats();
  const coderHooks = makeCoderHooks(pluginDir, join(repoRoot, 'dist'), hookStats);
  const startTs = Date.now();
  let totalCost = 0;

  const cfg: OllamaConfig = { ...ollamaConfig(), ...(model ? { model } : {}) };
  if (engine === 'local') {
    const h = await ollamaHealth(cfg);
    if (!h.ok) {
      console.error('✖ ' + h.message);
      return 2;
    }
  }
  const idx = engine === 'local' ? await loadOrBuildIndex(cfg, repoRoot) : null;

  const verify = async (): Promise<PipelineResult> =>
    runPipeline(pluginDir, {
      repoRoot,
      harnessDir,
      wpEnv,
      onGate: (r) => process.stdout.write(`    [${r.skipped ? 'SKIP' : r.passed ? 'PASS' : 'FAIL'}] ${r.label}\n`),
    });

  /** Apply an instruction with the selected engine (initial change or a fix round). */
  const applyChange = async (instruction: string, pipe: PipelineResult | null): Promise<void> => {
    if (engine === 'claude') {
      const r = await runAgent({ repoRoot, role: coder, rulesAppend: rules, prompt: instruction, hooks: coderHooks, maxTurns: 120, maxBudgetUsd: 5 });
      totalCost += r.costUsd;
      console.log(`  coder done (${r.turns} turns, $${r.costUsd.toFixed(4)}).`);
      return;
    }
    // local engine — file protocol
    const failing = pipe ? pipe.results.filter((x) => !x.passed && !x.skipped) : [];
    const fixCtx = failing.length
      ? (formatFixes(failing.flatMap((x) => [...x.errors, ...x.notes])) + '\n\n' + formatContext(await retrieve(idx!, cfg, 'fix ' + failing.map((x) => x.label).join(' '), 4)))
      : formatContext(await retrieve(idx!, cfg, changeRequest + ' WordPress plugin security', 5));
    const snap = await snapshotFiles(pluginDir);
    const sys = `You are an expert WordPress developer editing an EXISTING plugin. Keep existing behavior unless asked to change it. Follow these hard rules:\n\n${rules}\n\nOUTPUT PROTOCOL: for every file you create or modify, output the ENTIRE file between markers:\n===FILE: relative/path.php===\n<content>\n===ENDFILE===\nNo prose, no markdown fences.`;
    const msgs: ChatMessage[] = [
      { role: 'system', content: sys },
      { role: 'user', content: `${instruction}\n\nRELEVANT RULES / FIXES:\n${fixCtx}\n\nCURRENT FILES:\n${snap}` },
    ];
    const out = await ollamaChat(cfg, msgs, { temperature: 0.1, numCtx: 32768, timeoutMs: 15 * 60_000 });
    const wr = await writeGeneratedFiles(parseFiles(out.content), pluginDir);
    console.log(`  wrote ${wr.written.length} file(s).`);
    await exec(join(harnessDir, 'vendor', 'bin', 'phpcbf'), ['-q', `--standard=${join(harnessDir, 'phpcs.xml.dist')}`, pluginDir], { timeoutMs: 120_000 });
  };

  // 4. apply the change
  console.log(`\n▶ Updating with ${engine === 'claude' ? 'Claude' : 'the local model'}…`);
  await applyChange(
    `You are updating the EXISTING WordPress plugin in build/${slug}/. Apply this change request, preserving all ` +
      `existing functionality unless the request says otherwise:\n\n"${changeRequest}"\n\nAlso make the plugin fully ` +
      `comply with the hard rules and pass all 8 gates (add ABSPATH guards, escape output, sanitize input, pair every ` +
      `state change with a nonce AND current_user_can(), use $wpdb->prepare(), etc. wherever missing). Bump the version ` +
      `to ${newVersion} in the plugin header, the readme.txt "Stable tag" (if present), and any *_VERSION constant, and ` +
      `add a "= ${newVersion} =" changelog entry to readme.txt if it has a changelog. When done, stop.`,
    null,
  );

  // 5. verify → fix loop
  console.log('\n▶ Verifying…');
  let pipe = await verify();
  let iterations = 0;
  while (!pipe.passed && iterations < MAX_FIX_ITERATIONS) {
    iterations++;
    console.log(`\n  Failed — fix iteration ${iterations}/${MAX_FIX_ITERATIONS}.`);
    await applyChange(`The verification harness FAILED. Fix ONLY these issues in build/${slug}/, then stop.\n\n${digestFailures(pipe)}`, pipe);
    console.log('\n▶ Re-verifying…');
    pipe = await verify();
  }

  const durMin = ((Date.now() - startTs) / 60000).toFixed(1);
  let zipPath2: string | null = null;
  if (pipe.passed) {
    await mkdir(join(repoRoot, 'dist'), { recursive: true });
    zipPath2 = join(repoRoot, 'dist', `${slug}.${newVersion}.zip`);
    const pkg = await exec('php', [join(harnessDir, 'bin', 'package.php'), pluginDir, slug, zipPath2], { timeoutMs: 60_000 });
    if (pkg.code === 0) console.log('\n▶ Packaged: ' + pkg.stdout.trim());
    else zipPath2 = null;
  } else {
    console.log('\n' + renderTerminal(pipe));
  }

  const report = [
    `# Update report: ${root.name}`,
    '',
    `- **Slug:** \`${slug}\`  ·  **Version:** ${root.version} → ${newVersion}  ·  **Engine:** ${engine}`,
    `- **Change:** ${changeRequest}`,
    `- **Outcome:** ${pipe.passed && zipPath2 ? '✅ updated & verified' : '❌ did not pass all gates'}  ·  ${iterations} fix iteration(s)  ·  ${durMin} min`,
    '',
    renderMarkdown(pipe),
  ].join('\n');
  await mkdir(join(repoRoot, 'dist'), { recursive: true });
  await writeFile(join(repoRoot, 'dist', `${slug}-update-report.md`), report + '\n', 'utf8');

  await emitResult({
    ok: pipe.passed,
    engine: engine === 'claude' ? 'claude' : 'local',
    slug,
    pluginName: root.name,
    version: newVersion,
    zip: zipPath2,
    report: join(repoRoot, 'dist', `${slug}-update-report.md`),
    iterations,
    costUsd: totalCost,
    provides: await scanProvides(pluginDir),
    error: pipe.passed ? undefined : 'did not pass all gates',
  });

  console.log('\n' + '='.repeat(64));
  console.log(`${pipe.passed ? '✔' : '✖'} UPDATE: ${root.name} ${root.version} → ${newVersion}`);
  if (zipPath2) console.log(`   .zip: ${zipPath2}`);
  console.log('='.repeat(64));
  return pipe.passed ? 0 : 1;
}
