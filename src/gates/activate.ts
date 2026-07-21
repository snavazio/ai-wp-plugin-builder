/**
 * Gate 6 — activate: install + activate the plugin in wp-env, confirm active, scan debug.log
 * for PHP fatals. Fail on non-zero activate, inactive status, or any fatal.
 */
import type { GateContext, GateDefinition, GateResult } from '../types.js';
import { emptyResult, finalize, skipped } from './_util.js';

async function run(ctx: GateContext): Promise<GateResult> {
  const start = Date.now();
  const label = 'Install + activate (wp-env)';
  if (!ctx.dockerAvailable || !ctx.wpEnv) {
    return skipped('activate', label, true, 'Docker/wp-env unavailable — activation skipped.', start);
  }
  const r = emptyResult('activate', label, true);
  const env = ctx.wpEnv;

  // Start clean: deactivate any prior mount of this slug and clear the debug log.
  await env.wp(['plugin', 'deactivate', ctx.slug]);
  await env.clearDebugLog();

  // Activate.
  const act = await env.wp(['plugin', 'activate', ctx.slug]);
  if (act.code !== 0) {
    r.errors.push(`wp plugin activate ${ctx.slug} failed (exit ${act.code}).`);
    r.notes.push((act.stderr || act.stdout).slice(0, 2000));
  }

  // Confirm active.
  const isActive = await env.wp(['plugin', 'is-active', ctx.slug]);
  if (isActive.code !== 0) {
    r.errors.push(`Plugin "${ctx.slug}" is not active after activation attempt.`);
  }

  // Scan debug.log for fatals produced during activation / a follow-up page load.
  await env.wp(['eval', 'echo "ping";']); // force-load WP to surface any init-time fatals in the log
  const log = await env.readDebugLog();
  const fatalLines = log
    .split('\n')
    .filter((l) => /PHP (Fatal|Parse) error/i.test(l));
  if (fatalLines.length > 0) {
    for (const l of fatalLines.slice(0, 20)) r.errors.push(l.trim());
    r.notes.push(`${fatalLines.length} fatal/parse error line(s) in debug.log.`);
  } else {
    r.notes.push('No PHP fatals in debug.log.');
  }

  return finalize(r, start);
}

export const activateGate: GateDefinition = {
  gate: 'activate',
  label: 'Install + activate (wp-env)',
  requiresDocker: true,
  run,
};
