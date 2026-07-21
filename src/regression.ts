/**
 * Phase 3 — golden-set regression. Asserts KNOWN gate outcomes so a change to the harness, gates, or
 * templates can't silently break the trust model. Fully local/offline.
 *
 *   npm run regression              (full: needs Docker)
 *   npm run regression -- --static  (fast, no Docker; asserts static gates only)
 *
 * This is the guardrail that would have caught the phpcs "build-dir excluded -> false pass" bug:
 * the bad-plugin case asserts phpcs ACTUALLY reports a security error, not merely that the run failed.
 */
import { join } from 'node:path';
import { runPipeline } from './pipeline.js';
import { findPluginDirs } from './verifyAll.js';
import type { BuildEnv } from './build.js';
import type { GateResult } from './types.js';

type Expectation =
  | { kind: 'pass-all' }
  // gate id -> substring that MUST appear in that gate's errors (proves the gate really caught it)
  | { kind: 'fail'; gates: Record<string, string> };

interface Golden {
  name: string;
  dir: string;
  expect: Expectation;
}

interface CaseResult {
  name: string;
  ok: boolean;
  reasons: string[];
}

function gate(results: GateResult[], id: string): GateResult | undefined {
  return results.find((g) => g.gate === id);
}

function assertPassAll(results: GateResult[], staticOnly: boolean): string[] {
  const reasons: string[] = [];
  for (const g of results) {
    if (staticOnly && g.requiresDocker) continue; // docker gates are skipped in --static
    if (!g.passed) reasons.push(`gate "${g.gate}" expected PASS but was ${g.skipped ? 'SKIP' : 'FAIL'}`);
  }
  return reasons;
}

function assertFail(results: GateResult[], gates: Record<string, string>): string[] {
  const reasons: string[] = [];
  for (const [id, substr] of Object.entries(gates)) {
    const g = gate(results, id);
    if (!g) {
      reasons.push(`expected gate "${id}" to run, but it was absent`);
      continue;
    }
    if (g.passed || g.skipped) {
      reasons.push(`gate "${id}" expected FAIL but was ${g.skipped ? 'SKIP' : 'PASS'}`);
      continue;
    }
    const hit = g.errors.some((e) => e.toLowerCase().includes(substr.toLowerCase()));
    if (!hit) reasons.push(`gate "${id}" failed but no error contained "${substr}" (hollow gate?)`);
  }
  return reasons;
}

export async function runRegression(args: string[], env: BuildEnv): Promise<number> {
  const staticOnly = args.includes('--static');
  const { repoRoot, harnessDir } = env;

  // Static golden cases: the two samples with their known outcomes.
  const golden: Golden[] = [
    { name: 'samples/good-plugin', dir: join(repoRoot, 'samples', 'good-plugin'), expect: { kind: 'pass-all' } },
    {
      name: 'samples/bad-plugin',
      dir: join(repoRoot, 'samples', 'bad-plugin'),
      // The bad plugin MUST be caught: staticStructure on the missing ABSPATH guard, and phpcs on a
      // security sniff. Requiring the error substring guards against a gate that runs but checks nothing.
      expect: { kind: 'fail', gates: { staticStructure: 'ABSPATH', phpcs: 'Security' } },
    },
  ];
  // Every committed example must stay green.
  for (const dir of await findPluginDirs(join(repoRoot, 'examples'))) {
    golden.push({ name: `examples/${dir.split('/').pop()}`, dir, expect: { kind: 'pass-all' } });
  }

  console.log(`\nGolden-set regression (${golden.length} cases${staticOnly ? ', static only' : ''})…\n`);
  const caseResults: CaseResult[] = [];
  for (const c of golden) {
    const pipe = await runPipeline(c.dir, { repoRoot, harnessDir, noDocker: staticOnly });
    const reasons =
      c.expect.kind === 'pass-all' ? assertPassAll(pipe.results, staticOnly) : assertFail(pipe.results, c.expect.gates);
    const ok = reasons.length === 0;
    caseResults.push({ name: c.name, ok, reasons });
    console.log(`  ${ok ? '✅' : '❌'} ${c.name}${ok ? '' : '\n      - ' + reasons.join('\n      - ')}`);
  }

  const passed = caseResults.filter((c) => c.ok).length;
  console.log(`\n${passed}/${caseResults.length} golden cases held.`);
  if (passed !== caseResults.length) {
    console.error('\n✖ REGRESSION: the harness no longer produces expected outcomes. Investigate before shipping.');
    return 1;
  }
  console.log('✔ All golden expectations hold.');
  return 0;
}
