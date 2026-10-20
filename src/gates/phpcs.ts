/**
 * Gate 3 — phpcs: run PHP_CodeSniffer with the WordPress standard (incl. security sniffs).
 * Errors fail the gate; warnings are reported. Uses JSON output for structured parsing.
 */
import type { GateContext, GateDefinition, GateResult } from '../types.js';
import { exec } from '../util/exec.js';
import { join, relative } from 'node:path';
import { readFile } from 'node:fs/promises';
import { findPhpFiles } from '../util/exec.js';
import { emptyResult, finalize } from './_util.js';

interface PhpcsJson {
  totals?: { errors?: number; warnings?: number };
  files?: Record<
    string,
    { messages?: Array<{ message: string; source: string; severity: number; type: string; line: number }> }
  >;
}

async function run(ctx: GateContext): Promise<GateResult> {
  const start = Date.now();
  const r = emptyResult('phpcs', 'PHP_CodeSniffer (WordPress)', false);
  const phpcs = join(ctx.harnessDir, 'vendor', 'bin', 'phpcs');
  const standard = join(ctx.harnessDir, 'phpcs.xml.dist');

  // Judge version-dependent sniffs (e.g. the %i identifier placeholder, WP 6.2+) against the WordPress
  // version the plugin itself requires, not the ruleset's default floor.
  const runtime: string[] = [];
  const requiresWp = await requiredWp(ctx.pluginDir);
  if (requiresWp) {
    runtime.push('--runtime-set', 'minimum_wp_version', requiresWp);
    r.notes.push(`phpcs minimum_wp_version: ${requiresWp} (from the plugin header).`);
  }

  const res = await exec(
    phpcs,
    ['-q', '--standard=' + standard, '--report=json', ...runtime, ctx.pluginDir],
    { timeoutMs: 5 * 60_000 },
  );

  if (res.spawnError) {
    r.errors.push(`Could not run phpcs: ${res.spawnError}`);
    return finalize(r, start);
  }

  let json: PhpcsJson;
  try {
    json = JSON.parse(res.stdout);
  } catch {
    r.errors.push('phpcs produced unparseable output.');
    r.notes.push((res.stdout || res.stderr).slice(0, 2000));
    return finalize(r, start);
  }

  const errCount = json.totals?.errors ?? 0;
  const warnCount = json.totals?.warnings ?? 0;
  r.notes.push(`phpcs: ${errCount} errors, ${warnCount} warnings.`);

  for (const [file, data] of Object.entries(json.files ?? {})) {
    const rel = relative(ctx.pluginDir, file);
    for (const msg of data.messages ?? []) {
      const line = `${rel}:${msg.line} [${msg.source}] ${msg.message}`;
      if (msg.type === 'ERROR') r.errors.push(line);
      else r.warnings.push(line);
    }
  }

  return finalize(r, start);
}

/** "Requires at least" from the main plugin file header, if it is a plain version number. */
async function requiredWp(pluginDir: string): Promise<string | null> {
  for (const f of await findPhpFiles(pluginDir)) {
    if (/\/tests\//.test(f)) continue;
    const src = await readFile(f, 'utf8').catch(() => '');
    if (!/^[ \t/*#@]*Plugin Name\s*:/im.test(src)) continue;
    const v = src.match(/^[ \t/*#@]*Requires at least\s*:\s*([0-9]+\.[0-9]+(?:\.[0-9]+)?)\s*$/im)?.[1];
    return v ?? null;
  }
  return null;
}

export const phpcsGate: GateDefinition = {
  gate: 'phpcs',
  label: 'PHP_CodeSniffer (WordPress)',
  requiresDocker: false,
  run,
};
