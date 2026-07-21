/**
 * Phase 2 — revision loop: apply a natural-language change request to an ALREADY-BUILT plugin,
 * re-verify it through the full harness, re-audit, and re-package at a bumped version.
 *
 *   npm run revise -- <slug|path> "your change request" [--version X.Y.Z]
 *
 * Reuses the same coder → verify → auditor loop as `build` (from build.ts), so a revised plugin is
 * held to the exact same 8-gate + independent-auditor bar as a fresh one.
 */
import { readFile, writeFile, mkdir, cp, access } from 'node:fs/promises';
import { basename, join, resolve, isAbsolute } from 'node:path';
import {
  runAgent,
  digestFailures,
  parseFindings,
  blockingFindings,
  writeReport,
  MAX_FIX_ITERATIONS,
  MAX_AUDIT_ROUNDS,
  type BuildEnv,
} from './build.js';
import { loadRules, coder, securityAuditor } from './agents.js';
import { makeCoderHooks, newHookStats, type HookStats } from './hooks.js';
import { runPipeline } from './pipeline.js';
import { renderTerminal } from './report.js';
import { WpEnv, dockerAvailable } from './wpEnv.js';
import { exec } from './util/exec.js';
import { normalizeSpec, type StructuredSpec } from './spec.js';
import type { PipelineResult } from './types.js';

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
  const [, maj, min, patch] = m;
  return `${maj}.${min}.${(parseInt(patch ?? '0', 10) + 1).toString()}`;
}

async function readHeaderVersion(mainFile: string): Promise<string | null> {
  try {
    const src = await readFile(mainFile, 'utf8');
    const m = src.match(/^[ \t/*#@]*Version\s*:\s*(.+)$/im);
    return m ? m[1].trim() : null;
  } catch {
    return null;
  }
}

/** Resolve the source plugin directory from a slug or a path. */
async function resolveSource(target: string, repoRoot: string): Promise<{ dir: string; slug: string } | null> {
  const candidates = isAbsolute(target)
    ? [target]
    : [resolve(repoRoot, target), join(repoRoot, 'examples', target), join(repoRoot, 'build', target)];
  for (const c of candidates) {
    if (await exists(join(c, `${basename(c)}.php`))) return { dir: c, slug: basename(c) };
    // Also accept a dir that contains a *.php main file even if name differs from slug.
    if (await exists(c)) {
      const slug = basename(c);
      if (await exists(join(c, `${slug}.php`))) return { dir: c, slug };
    }
  }
  return null;
}

/** Build a minimal spec if SPEC.json is absent (e.g. revising a hand-written plugin). */
async function loadOrSynthesizeSpec(dir: string, slug: string, version: string): Promise<StructuredSpec> {
  const specPath = join(dir, 'SPEC.json');
  if (await exists(specPath)) {
    const spec = normalizeSpec(JSON.parse(await readFile(specPath, 'utf8'))) as unknown as StructuredSpec;
    spec.version = version;
    return spec;
  }
  const mainSrc = await readFile(join(dir, `${slug}.php`), 'utf8').catch(() => '');
  const name = mainSrc.match(/Plugin Name\s*:\s*(.+)/i)?.[1]?.trim() ?? slug;
  const desc = mainSrc.match(/Description\s*:\s*(.+)/i)?.[1]?.trim() ?? '';
  const prefix = mainSrc.match(/define\(\s*'([A-Z0-9]{2,7})_VERSION'/)?.[1]?.toLowerCase() ?? slug.replace(/[^a-z]/g, '').slice(0, 5);
  return {
    slug,
    pluginName: name,
    description: desc,
    version,
    prefix,
    requiresWp: '6.0',
    requiresPhp: '7.4',
    capabilities: [],
    postTypes: [],
    taxonomies: [],
    adminPages: [],
    shortcodes: [],
    blocks: [],
    restEndpoints: [],
    ajaxActions: [],
    cronEvents: [],
    widgets: [],
    dataStorage: '',
    securityRequirements: [],
    smokeAssertions: [],
  };
}

export async function runRevise(args: string[], env: BuildEnv): Promise<number> {
  // Parse: <target> <change request...> [--version X.Y.Z]
  let forcedVersion: string | null = null;
  const positional: string[] = [];
  for (let i = 0; i < args.length; i++) {
    if (args[i] === '--version') {
      forcedVersion = args[++i] ?? null;
    } else {
      positional.push(args[i]);
    }
  }
  const target = positional[0];
  const changeRequest = positional.slice(1).join(' ').trim();
  if (!target || !changeRequest) {
    console.error('Usage: npm run revise -- <slug|path> "your change request" [--version X.Y.Z]');
    return 2;
  }
  if (!process.env.ANTHROPIC_API_KEY) {
    console.error('ANTHROPIC_API_KEY is not set — required for the revision loop.');
    return 2;
  }

  const { repoRoot, harnessDir } = env;
  const docker = await dockerAvailable();
  if (!docker) {
    console.error('Docker/wp-env is unavailable — cannot verify a revision. Aborting.');
    return 2;
  }

  const src = await resolveSource(target, repoRoot);
  if (!src) {
    console.error(`Could not find a plugin to revise for "${target}" (looked in ./, examples/, build/).`);
    return 1;
  }
  const { slug } = src;
  const currentVersion = (await readHeaderVersion(join(src.dir, `${slug}.php`))) ?? '1.0.0';
  const newVersion = forcedVersion ?? bumpPatch(currentVersion);

  console.log(`\n▶ Revising "${slug}"  ${currentVersion} → ${newVersion}`);
  console.log(`  Change: ${changeRequest}`);

  // Copy the source into the working dir (unless it's already there).
  const pluginDir = join(repoRoot, 'build', slug);
  if (resolve(src.dir) !== resolve(pluginDir)) {
    await mkdir(join(repoRoot, 'build'), { recursive: true });
    await cp(src.dir, pluginDir, {
      recursive: true,
      filter: (p) => !p.endsWith('.phpunit.result.cache') && !p.endsWith('BUILD-REPORT.md') && !p.includes(`${slug}.${currentVersion}.zip`),
    });
  }

  const spec = await loadOrSynthesizeSpec(pluginDir, slug, newVersion);
  await writeFile(join(pluginDir, 'SPEC.json'), JSON.stringify(spec, null, 2) + '\n', 'utf8');

  const rules = await loadRules(repoRoot);
  const wpEnv = new WpEnv(repoRoot, pluginDir, slug, harnessDir);
  const hookStats: HookStats = newHookStats();
  const coderHooks = makeCoderHooks(pluginDir, join(repoRoot, 'dist'), hookStats);
  const startTs = Date.now();
  let totalCost = 0;
  const runLog: string[] = [`Revision of ${slug}: ${currentVersion} → ${newVersion}. Change: ${changeRequest}`];

  const verify = async (): Promise<PipelineResult> =>
    runPipeline(pluginDir, {
      repoRoot,
      harnessDir,
      wpEnv,
      onGate: (r) => process.stdout.write(`    [${r.skipped ? 'SKIP' : r.passed ? 'PASS' : 'FAIL'}] ${r.label}\n`),
    });

  const runCoder = async (prompt: string, label: string): Promise<void> => {
    console.log(`\n▶ coder: ${label}…`);
    const r = await runAgent({ repoRoot, role: coder, rulesAppend: rules, prompt, hooks: coderHooks, maxTurns: 120, maxBudgetUsd: 5 });
    totalCost += r.costUsd;
    console.log(`  coder done (${r.turns} turns, $${r.costUsd.toFixed(4)}).`);
  };

  // ---------- apply the change ----------
  await runCoder(
    `Apply this change request to the EXISTING plugin in build/${slug}/ (do not rewrite it from scratch — edit ` +
      `the existing code):\n\n"${changeRequest}"\n\n` +
      `Also bump the version to ${newVersion} everywhere it appears (the plugin header "Version", readme.txt ` +
      `"Stable tag", and the *_VERSION constant), and add a matching "= ${newVersion} =" entry to the readme ` +
      `Changelog. Update tests/test-smoke.php and uninstall.php if the change adds features or stored data. ` +
      `Keep everything PHP_CodeSniffer-clean and follow all hard rules. When done, stop.`,
    'applying revision',
  );

  // ---------- verify → fix loop ----------
  console.log('\n▶ Verifying revision…');
  let pipe = await verify();
  let iterations = 0;
  while (!pipe.passed && iterations < MAX_FIX_ITERATIONS) {
    iterations++;
    console.log(`\n  Harness failed — fix iteration ${iterations}/${MAX_FIX_ITERATIONS}.`);
    await runCoder(`The verification harness FAILED. Fix ONLY these issues in build/${slug}/, then stop.\n\n${digestFailures(pipe)}`, `fix iteration ${iterations}`);
    console.log('\n▶ Re-verifying…');
    pipe = await verify();
  }
  if (!pipe.passed) {
    console.log('\n' + renderTerminal(pipe));
    console.error(`\n✖ Revision still failing after ${iterations} iterations. No .zip produced.`);
    await writeReport({ repoRoot, spec, pipe, findings: [], auditNote: 'Not reached — harness never passed.', hookStats, iterations, totalCost, startTs, zipPath: null, runLog });
    return 1;
  }
  console.log(`\n✔ Harness green after ${iterations} fix iteration(s).`);

  // ---------- independent audit ----------
  console.log('\n▶ security-auditor (independent, read-only)…');
  let auditRun = await runAgent({ repoRoot, role: securityAuditor, rulesAppend: rules, prompt: `Audit build/${slug}/ against the hard security rules. Output ONLY the findings JSON block.`, maxTurns: 40, maxBudgetUsd: 3 });
  totalCost += auditRun.costUsd;
  let { findings, parseNote } = parseFindings(auditRun.text);
  console.log(`  auditor: ${findings.length} finding(s).`);

  let rounds = 0;
  while (blockingFindings(findings).length > 0 && rounds < MAX_AUDIT_ROUNDS) {
    rounds++;
    const fixList = blockingFindings(findings).map((f) => `- [${f.severity}] ${f.file ?? '?'}:${f.line ?? '?'} — ${f.issue} → ${f.required_fix}`).join('\n');
    await runCoder(`The independent security auditor found issues. Fix ONLY these in build/${slug}/:\n${fixList}`, `auditor fix round ${rounds}`);
    console.log('\n▶ Re-verifying after auditor fixes…');
    pipe = await verify();
    if (!pipe.passed) {
      console.error('\n✖ Harness regressed after auditor fixes. No .zip produced.');
      await writeReport({ repoRoot, spec, pipe, findings, auditNote: parseNote, hookStats, iterations, totalCost, startTs, zipPath: null, runLog });
      return 1;
    }
    auditRun = await runAgent({ repoRoot, role: securityAuditor, rulesAppend: rules, prompt: `Re-audit build/${slug}/. Output ONLY the findings JSON block.`, maxTurns: 40, maxBudgetUsd: 3 });
    totalCost += auditRun.costUsd;
    ({ findings, parseNote } = parseFindings(auditRun.text));
  }
  if (blockingFindings(findings).length > 0) {
    console.error(`\n✖ Auditor still reports blocking findings after ${rounds} rounds. No .zip produced.`);
    await writeReport({ repoRoot, spec, pipe, findings, auditNote: parseNote, hookStats, iterations, totalCost, startTs, zipPath: null, runLog });
    return 1;
  }

  // ---------- package + report ----------
  console.log('\n▶ Packaging…');
  await mkdir(join(repoRoot, 'dist'), { recursive: true });
  const zipPath = join(repoRoot, 'dist', `${slug}.${newVersion}.zip`);
  const pkg = await exec('php', [join(harnessDir, 'bin', 'package.php'), pluginDir, slug, zipPath], { timeoutMs: 60_000 });
  if (pkg.code !== 0) {
    console.error('Packaging failed:', pkg.stderr || pkg.stdout);
    return 1;
  }
  console.log('  ' + pkg.stdout.trim());
  await writeReport({ repoRoot, spec, pipe, findings, auditNote: parseNote, hookStats, iterations, totalCost, startTs, zipPath, runLog });

  console.log('\n' + '='.repeat(64));
  console.log(`✔ REVISED: ${spec.pluginName}  ${currentVersion} → ${newVersion}`);
  console.log(`   .zip:   ${zipPath}`);
  console.log(`   report: dist/${slug}-report.md   ·   gates: all 8 PASS · auditor: clean · cost: $${totalCost.toFixed(4)}`);
  console.log('='.repeat(64));
  return 0;
}
