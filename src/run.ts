#!/usr/bin/env -S npx tsx
/**
 * Entrypoint CLI.
 *
 *   npm run verify -- <plugin-dir> [--no-docker] [--md <file>]
 *   npm run build  -- specs/<name>.md          (Phase 1)
 */
import { fileURLToPath } from 'node:url';
import { dirname, join, resolve } from 'node:path';
import { writeFile } from 'node:fs/promises';
import { runPipeline } from './pipeline.js';
import { renderTerminal, renderMarkdown } from './report.js';

const __dirname = dirname(fileURLToPath(import.meta.url));
const REPO_ROOT = resolve(__dirname, '..');
const HARNESS_DIR = join(REPO_ROOT, 'harness');

function parseFlags(args: string[]): { positional: string[]; flags: Record<string, string | boolean> } {
  const positional: string[] = [];
  const flags: Record<string, string | boolean> = {};
  for (let i = 0; i < args.length; i++) {
    const a = args[i];
    if (a.startsWith('--')) {
      const key = a.slice(2);
      const next = args[i + 1];
      if (next && !next.startsWith('--')) {
        flags[key] = next;
        i++;
      } else {
        flags[key] = true;
      }
    } else {
      positional.push(a);
    }
  }
  return { positional, flags };
}

async function cmdVerify(args: string[]): Promise<number> {
  const { positional, flags } = parseFlags(args);
  const target = positional[0];
  if (!target) {
    console.error('Usage: npm run verify -- <plugin-dir> [--no-docker] [--md <file>]');
    return 2;
  }

  console.log(`\nVerifying ${target} …`);
  const result = await runPipeline(target, {
    repoRoot: REPO_ROOT,
    harnessDir: HARNESS_DIR,
    noDocker: Boolean(flags['no-docker']),
    onGate: (r) => {
      const status = r.skipped ? 'SKIP' : r.passed ? 'PASS' : 'FAIL';
      process.stdout.write(`  [${status}] ${r.label}\n`);
    },
  });

  console.log(renderTerminal(result));

  if (typeof flags['md'] === 'string') {
    await writeFile(flags['md'], renderMarkdown(result), 'utf8');
    console.log(`Markdown report written to ${flags['md']}`);
  }

  return result.passed ? 0 : 1;
}

async function cmdBuild(args: string[]): Promise<number> {
  const { runBuild } = await import('./build.js');
  return runBuild(args, { repoRoot: REPO_ROOT, harnessDir: HARNESS_DIR });
}

async function cmdRevise(args: string[]): Promise<number> {
  const { runRevise } = await import('./revise.js');
  return runRevise(args, { repoRoot: REPO_ROOT, harnessDir: HARNESS_DIR });
}

async function cmdBuildLocal(args: string[]): Promise<number> {
  const { runBuildLocal } = await import('./buildLocal.js');
  return runBuildLocal(args, { repoRoot: REPO_ROOT, harnessDir: HARNESS_DIR });
}

async function cmdVerifyAll(args: string[]): Promise<number> {
  const { runVerifyAll } = await import('./verifyAll.js');
  return runVerifyAll(args, { repoRoot: REPO_ROOT, harnessDir: HARNESS_DIR });
}

async function cmdRegression(args: string[]): Promise<number> {
  const { runRegression } = await import('./regression.js');
  return runRegression(args, { repoRoot: REPO_ROOT, harnessDir: HARNESS_DIR });
}

async function main(): Promise<void> {
  const [, , command, ...rest] = process.argv;
  let code = 0;
  switch (command) {
    case 'verify':
      code = await cmdVerify(rest);
      break;
    case 'verify-all':
      code = await cmdVerifyAll(rest);
      break;
    case 'regression':
      code = await cmdRegression(rest);
      break;
    case 'build':
      code = await cmdBuild(rest);
      break;
    case 'build-local':
      code = await cmdBuildLocal(rest);
      break;
    case 'revise':
      code = await cmdRevise(rest);
      break;
    default:
      console.error(
        'Commands: verify <dir> | verify-all <dir> | regression | build <spec.md> | build-local <spec.md> | revise <slug> "change"',
      );
      code = 2;
  }
  process.exit(code);
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
