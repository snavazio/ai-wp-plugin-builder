/**
 * Gate 4 — phpstan: static analysis at level 5 with WordPress stubs (szepeviktor extension).
 * Errors fail the gate.
 */
import type { GateContext, GateDefinition, GateResult } from '../types.js';
import { exec } from '../util/exec.js';
import { join, relative } from 'node:path';
import { emptyResult, finalize } from './_util.js';

interface PhpstanJson {
  totals?: { errors?: number; file_errors?: number };
  files?: Record<string, { messages?: Array<{ message: string; line: number | null }> }>;
  errors?: string[];
}

async function run(ctx: GateContext): Promise<GateResult> {
  const start = Date.now();
  const r = emptyResult('phpstan', 'PHPStan (level 5, WP stubs)', false);
  const phpstan = join(ctx.harnessDir, 'vendor', 'bin', 'phpstan');
  const config = join(ctx.harnessDir, 'phpstan.neon');

  const res = await exec(
    phpstan,
    ['analyse', '--no-progress', '--error-format=json', '--configuration=' + config, ctx.pluginDir],
    { timeoutMs: 5 * 60_000, cwd: ctx.harnessDir },
  );

  if (res.spawnError) {
    r.errors.push(`Could not run phpstan: ${res.spawnError}`);
    return finalize(r, start);
  }

  let json: PhpstanJson;
  try {
    json = JSON.parse(res.stdout);
  } catch {
    r.errors.push('phpstan produced unparseable output.');
    r.notes.push((res.stdout || res.stderr).slice(0, 2000));
    return finalize(r, start);
  }

  // Global (non-file) errors — usually config/analysis problems.
  for (const e of json.errors ?? []) r.errors.push(`[phpstan] ${e}`);

  for (const [file, data] of Object.entries(json.files ?? {})) {
    const rel = relative(ctx.pluginDir, file);
    for (const msg of data.messages ?? []) {
      r.errors.push(`${rel}:${msg.line ?? '?'} ${msg.message}`);
    }
  }

  r.notes.push(`phpstan: ${json.totals?.file_errors ?? 0} file errors, ${json.totals?.errors ?? 0} total.`);
  return finalize(r, start);
}

export const phpstanGate: GateDefinition = {
  gate: 'phpstan',
  label: 'PHPStan (level 5, WP stubs)',
  requiresDocker: false,
  run,
};
