/**
 * Gate 1 — phpLint: run `php -l` on every .php file. Fail on any parse error.
 */
import type { GateContext, GateDefinition, GateResult } from '../types.js';
import { exec, findPhpFiles } from '../util/exec.js';
import { relative } from 'node:path';
import { emptyResult, finalize } from './_util.js';

async function run(ctx: GateContext): Promise<GateResult> {
  const start = Date.now();
  const r = emptyResult('phpLint', 'PHP lint (php -l)', false);
  const files = await findPhpFiles(ctx.pluginDir);
  if (files.length === 0) {
    r.errors.push('No .php files found in plugin directory.');
    return finalize(r, start);
  }
  let ok = 0;
  for (const file of files) {
    const res = await exec('php', ['-l', file], { timeoutMs: 30_000 });
    if (res.code !== 0) {
      const msg = (res.stderr || res.stdout).trim().split('\n').filter(Boolean).join(' ');
      r.errors.push(`${relative(ctx.pluginDir, file)}: ${msg}`);
    } else {
      ok++;
    }
  }
  r.notes.push(`${ok}/${files.length} files lint-clean.`);
  return finalize(r, start);
}

export const phpLintGate: GateDefinition = {
  gate: 'phpLint',
  label: 'PHP lint (php -l)',
  requiresDocker: false,
  run,
};
