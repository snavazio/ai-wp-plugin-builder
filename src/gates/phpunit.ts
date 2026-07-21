/**
 * Gate 7 — phpunit: run the plugin's PHPUnit smoke test inside the wp-env tests-cli container,
 * using the WordPress test framework wp-env provides and the phpunit.phar mapped via harness/.
 *
 * If the plugin ships no phpunit config, the gate passes with a warning (nothing to run).
 */
import type { GateContext, GateDefinition, GateResult } from '../types.js';
import { HARNESS_MOUNT } from '../wpEnv.js';
import { emptyResult, finalize, skipped } from './_util.js';
import { access } from 'node:fs/promises';
import { join } from 'node:path';

const PHAR_IN_CONTAINER = `/var/www/html/${HARNESS_MOUNT}/bin/phpunit.phar`;

async function exists(p: string): Promise<boolean> {
  try {
    await access(p);
    return true;
  } catch {
    return false;
  }
}

async function run(ctx: GateContext): Promise<GateResult> {
  const start = Date.now();
  const label = 'PHPUnit smoke test';
  if (!ctx.dockerAvailable || !ctx.wpEnv) {
    return skipped('phpunit', label, true, 'Docker/wp-env unavailable — smoke test skipped.', start);
  }
  const r = emptyResult('phpunit', label, true);
  const env = ctx.wpEnv;

  const hasConfig =
    (await exists(join(ctx.pluginDir, 'phpunit.xml.dist'))) || (await exists(join(ctx.pluginDir, 'phpunit.xml')));
  if (!hasConfig) {
    r.warnings.push('No phpunit.xml(.dist) in plugin — no smoke test to run.');
    r.passed = true;
    r.durationMs = Date.now() - start;
    return r;
  }

  const cwd = `wp-content/plugins/${ctx.slug}`;
  const res = await env.run(
    'tests-cli',
    ['php', PHAR_IN_CONTAINER, '-c', 'phpunit.xml.dist', '--colors=never'],
    ['--env-cwd=' + cwd],
  );

  const out = (res.stdout + '\n' + res.stderr).trim();
  if (res.code !== 0) {
    r.errors.push(`PHPUnit failed (exit ${res.code}).`);
    // Keep the tail of the output so failures are diagnosable but not overwhelming.
    r.notes.push(out.split('\n').slice(-40).join('\n'));
  } else {
    const summary = out.split('\n').filter((l) => /OK|Tests:|Assertions:/i.test(l)).slice(-3).join(' ');
    r.notes.push(summary || 'PHPUnit passed.');
  }

  return finalize(r, start);
}

export const phpunitGate: GateDefinition = {
  gate: 'phpunit',
  label: 'PHPUnit smoke test',
  requiresDocker: true,
  run,
};
