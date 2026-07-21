/**
 * Pipeline: run the ordered gates against a plugin directory.
 *
 * Rules:
 *  - Cheap static gates (no Docker) ALWAYS run, so you get a full static picture even on failure.
 *  - Expensive wp-env gates run only if (a) Docker is available and (b) nothing has hard-failed yet
 *    (short-circuit on first hard failure).
 *  - Overall pass requires every gate to have `passed` true (a skipped gate is not a pass).
 */
import { basename, resolve } from 'node:path';
import type { GateContext, GateResult, PipelineResult } from './types.js';
import { GATES } from './gates/index.js';
import { WpEnv, dockerAvailable } from './wpEnv.js';
import { skipped } from './gates/_util.js';

export interface RunOptions {
  repoRoot: string;
  harnessDir: string;
  /** Force-skip the Docker gates (e.g. for a fast static-only pass). */
  noDocker?: boolean;
  /** Called after each gate finishes, for live output. */
  onGate?: (r: GateResult) => void;
  /** Reuse an already-started WpEnv (Phase 1 loop) instead of starting a fresh one. */
  wpEnv?: WpEnv;
}

export async function runPipeline(pluginDirInput: string, opts: RunOptions): Promise<PipelineResult> {
  const pluginDir = resolve(pluginDirInput);
  const slug = basename(pluginDir);
  const start = Date.now();

  const needsDocker = GATES.some((g) => g.requiresDocker);
  const docker = opts.noDocker ? false : needsDocker ? await dockerAvailable() : false;

  let env: WpEnv | undefined = opts.wpEnv;
  if (docker && !env) {
    env = new WpEnv(opts.repoRoot, pluginDir, slug, opts.harnessDir);
  }

  const ctx: GateContext = {
    pluginDir,
    slug,
    harnessDir: opts.harnessDir,
    repoRoot: opts.repoRoot,
    dockerAvailable: docker,
    wpEnv: env,
  };

  const results: GateResult[] = [];
  let hardFailed = false;
  let envStarted = env?.isStarted() ?? false;

  for (const gate of GATES) {
    const gateStart = Date.now();

    if (gate.requiresDocker) {
      if (!docker) {
        const r = skipped(gate.gate, gate.label, true, 'Docker/wp-env unavailable.', gateStart);
        results.push(r);
        opts.onGate?.(r);
        continue;
      }
      if (hardFailed) {
        const r = skipped(gate.gate, gate.label, true, 'Skipped — an earlier gate hard-failed.', gateStart);
        results.push(r);
        opts.onGate?.(r);
        continue;
      }
      // Lazily start wp-env before the first Docker gate.
      if (env && !envStarted) {
        const startRes = await env.start();
        envStarted = startRes.code === 0;
        if (!envStarted) {
          const r = skipped(gate.gate, gate.label, true, 'wp-env failed to start.', gateStart);
          r.errors.push('wp-env start failed:');
          r.notes.push((startRes.stderr || startRes.stdout).slice(-2000));
          r.skipped = false;
          results.push(r);
          opts.onGate?.(r);
          hardFailed = true;
          continue;
        }
      }
    }

    const r = await gate.run(ctx);
    results.push(r);
    opts.onGate?.(r);
    if (!r.passed && !r.skipped) hardFailed = true;
  }

  const passed = results.every((r) => r.passed);
  return {
    slug,
    pluginDir,
    passed,
    results,
    totalDurationMs: Date.now() - start,
    dockerAvailable: docker,
  };
}
