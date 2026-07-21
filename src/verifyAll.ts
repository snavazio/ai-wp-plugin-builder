/**
 * Phase 3 — batch verification. Run the full harness over every plugin in a directory and print a
 * combined pass/fail table. Fully local and API-free (PHP tools + Docker/wp-env only).
 *
 *   npm run verify-all -- [dir]            (default: examples/)
 *   npm run verify-all -- examples --no-docker
 *   npm run verify-all -- examples --md report.md
 */
import { readdir, readFile, writeFile } from 'node:fs/promises';
import { join, resolve } from 'node:path';
import { runPipeline } from './pipeline.js';
import type { BuildEnv } from './build.js';
import type { PipelineResult } from './types.js';

async function isPluginDir(dir: string): Promise<boolean> {
  try {
    const entries = await readdir(dir, { withFileTypes: true });
    for (const e of entries) {
      if (e.isFile() && e.name.endsWith('.php')) {
        const src = await readFile(join(dir, e.name), 'utf8');
        if (/^[ \t/*#@]*Plugin Name\s*:/im.test(src)) return true;
      }
    }
  } catch {
    /* ignore */
  }
  return false;
}

/** Find immediate subdirectories that are plugins; if `root` itself is a plugin, use just that. */
export async function findPluginDirs(root: string): Promise<string[]> {
  if (await isPluginDir(root)) return [root];
  const out: string[] = [];
  let entries;
  try {
    entries = await readdir(root, { withFileTypes: true });
  } catch {
    return out;
  }
  for (const e of entries) {
    if (e.isDirectory()) {
      const dir = join(root, e.name);
      if (await isPluginDir(dir)) out.push(dir);
    }
  }
  return out.sort();
}

export async function runVerifyAll(args: string[], env: BuildEnv): Promise<number> {
  const noDocker = args.includes('--no-docker');
  const mdIdx = args.indexOf('--md');
  const mdPath = mdIdx >= 0 ? args[mdIdx + 1] : null;
  const target = args.find((a) => !a.startsWith('--') && a !== mdPath) ?? 'examples';
  const root = resolve(env.repoRoot, target);

  const pluginDirs = await findPluginDirs(root);
  if (pluginDirs.length === 0) {
    console.error(`No plugin folders found under ${root}.`);
    return 2;
  }

  console.log(`\nVerifying ${pluginDirs.length} plugin(s) under ${target}${noDocker ? ' (static only)' : ''}…\n`);
  const results: PipelineResult[] = [];
  for (const dir of pluginDirs) {
    process.stdout.write(`  ${dir.split('/').pop()!.padEnd(22)} `);
    const r = await runPipeline(dir, { repoRoot: env.repoRoot, harnessDir: env.harnessDir, noDocker });
    results.push(r);
    const firstFail = r.results.find((g) => !g.passed && !g.skipped);
    process.stdout.write(r.passed ? '✅ PASS\n' : `❌ FAIL  (${firstFail?.label ?? 'incomplete'})\n`);
  }

  const passed = results.filter((r) => r.passed).length;
  console.log(`\n${passed}/${results.length} passed.`);

  if (mdPath) {
    const lines = ['# Batch verification', '', `${passed}/${results.length} passed.`, '', '| Plugin | Result | First failing gate |', '| --- | --- | --- |'];
    for (const r of results) {
      const ff = r.results.find((g) => !g.passed && !g.skipped);
      lines.push(`| \`${r.slug}\` | ${r.passed ? '✅ pass' : '❌ fail'} | ${r.passed ? '—' : ff?.label ?? 'incomplete'} |`);
    }
    await writeFile(mdPath, lines.join('\n') + '\n', 'utf8');
    console.log(`Markdown written to ${mdPath}`);
  }

  return passed === results.length ? 0 : 1;
}
