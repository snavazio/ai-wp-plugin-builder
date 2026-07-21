/**
 * Gate 5 — pluginCheck: run the official WordPress "Plugin Check" (plugin-check / PCP) via WP-CLI
 * inside wp-env. ANY security error = hard fail. Other categories are reported as warnings.
 */
import type { GateContext, GateDefinition, GateResult } from '../types.js';
import { emptyResult, finalize, skipped } from './_util.js';

interface PcRow {
  line?: number;
  column?: number;
  type?: string; // ERROR | WARNING
  code?: string;
  message?: string;
  file?: string;
}

/**
 * `wp plugin check --format=json` emits ONE JSON array PER FILE, each preceded by a
 * `FILE: <path>` header line and interleaved with wp-env's own "ℹ Starting" / "✔ Ran" noise.
 * It is NOT a single JSON document. Parse each per-file array and tag rows with their file.
 * (A naive JSON.parse(stdout) throws and would silently hide all findings — a false pass.)
 */
function parseRows(stdout: string): PcRow[] {
  const rows: PcRow[] = [];
  let currentFile = '';
  for (const raw of stdout.split('\n')) {
    const line = raw.trim();
    const fileMatch = line.match(/^FILE:\s*(.+)$/);
    if (fileMatch) {
      currentFile = fileMatch[1].trim();
      continue;
    }
    if (line.startsWith('[') && line.endsWith(']')) {
      try {
        const arr = JSON.parse(line);
        if (Array.isArray(arr)) {
          for (const o of arr) rows.push({ ...o, file: o.file ?? currentFile });
        }
      } catch {
        /* not a findings array — ignore */
      }
    }
  }
  return rows;
}

async function run(ctx: GateContext): Promise<GateResult> {
  const start = Date.now();
  const label = 'WordPress Plugin Check';
  if (!ctx.dockerAvailable || !ctx.wpEnv) {
    return skipped('pluginCheck', label, true, 'Docker/wp-env unavailable — Plugin Check skipped.', start);
  }
  const r = emptyResult('pluginCheck', label, true);
  const env = ctx.wpEnv;

  // Ensure the plugin-check plugin is installed & active (persisted in the docker volume once done).
  const isInstalled = await env.wp(['plugin', 'is-installed', 'plugin-check']);
  if (isInstalled.code !== 0) {
    const install = await env.wp(['plugin', 'install', 'plugin-check', '--activate']);
    if (install.code !== 0) {
      r.errors.push('Failed to install the plugin-check plugin into wp-env.');
      r.notes.push((install.stderr || install.stdout).slice(0, 1500));
      return finalize(r, start);
    }
  } else {
    await env.wp(['plugin', 'activate', 'plugin-check']);
  }

  // Dev files/dirs are excluded from the shipped .zip (.distignore) and legitimately can't carry
  // ABSPATH guards (test bootstraps run pre-WordPress), so mirror that exclusion for Plugin Check
  // to check the plugin as it will actually ship.
  const excludeDirs = '--exclude-directories=tests,vendor,node_modules';
  const excludeFiles = '--exclude-files=phpunit.xml.dist,phpunit.xml,.distignore,SPEC.json,.phpunit.result.cache';

  // Security check — the hard gate.
  const sec = await env.wp([
    'plugin',
    'check',
    ctx.slug,
    '--categories=security',
    '--format=json',
    '--severity=5',
    excludeDirs,
    excludeFiles,
  ]);
  const secRows = parseRows(sec.stdout);
  const secErrors = secRows.filter((x) => (x.type ?? '').toUpperCase() === 'ERROR');
  for (const row of secErrors) {
    r.errors.push(`[security] ${row.file ?? ctx.slug}:${row.line ?? '?'} (${row.code ?? '?'}) ${row.message ?? ''}`);
  }
  for (const row of secRows.filter((x) => (x.type ?? '').toUpperCase() === 'WARNING')) {
    r.warnings.push(`[security] ${row.file ?? ctx.slug}:${row.line ?? '?'} (${row.code ?? '?'}) ${row.message ?? ''}`);
  }
  if (secRows.length === 0 && sec.code !== 0 && sec.stdout.trim() === '') {
    r.warnings.push('Plugin Check (security) returned no parseable JSON; raw: ' + (sec.stderr || sec.stdout).slice(0, 500));
  }

  // Other categories — reported as warnings, not hard failures.
  const other = await env.wp([
    'plugin',
    'check',
    ctx.slug,
    '--categories=plugin_repo,performance,i18n',
    '--format=json',
    excludeDirs,
    excludeFiles,
  ]);
  for (const row of parseRows(other.stdout)) {
    r.warnings.push(
      `[${(row.code ?? 'check')}] ${row.file ?? ctx.slug}:${row.line ?? '?'} (${row.type ?? '?'}) ${row.message ?? ''}`,
    );
  }

  r.notes.push(`Security: ${secErrors.length} errors. Other categories: ${parseRows(other.stdout).length} findings.`);
  return finalize(r, start);
}

export const pluginCheckGate: GateDefinition = {
  gate: 'pluginCheck',
  label: 'WordPress Plugin Check',
  requiresDocker: true,
  run,
};
