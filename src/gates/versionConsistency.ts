/**
 * Gate 8 — versionConsistency: assert header Version == readme Stable tag == any *_VERSION constant.
 */
import type { GateContext, GateDefinition, GateResult } from '../types.js';
import { findPhpFiles } from '../util/exec.js';
import { readFile } from 'node:fs/promises';
import { join } from 'node:path';
import { emptyResult, finalize } from './_util.js';
import { readFileSafe } from '../wpEnv.js';

function headerVersion(src: string): string | null {
  const m = src.match(/^[ \t/*#@]*Version\s*:\s*(.+)$/im);
  return m ? m[1].trim() : null;
}

function stableTag(readme: string): string | null {
  const m = readme.match(/^\s*Stable tag\s*:\s*(.+)$/im);
  return m ? m[1].trim() : null;
}

async function run(ctx: GateContext): Promise<GateResult> {
  const start = Date.now();
  const r = emptyResult('versionConsistency', 'Version consistency', false);

  // Header version from the main plugin file.
  const files = await findPhpFiles(ctx.pluginDir);
  let header: string | null = null;
  const constants: Array<{ name: string; value: string }> = [];
  for (const file of files) {
    const src = await readFile(file, 'utf8');
    if (header === null && /^[ \t/*#@]*Plugin Name\s*:/im.test(src)) {
      header = headerVersion(src);
    }
    // Collect `define( 'XXX_VERSION', '1.2.3' )` and `const XXX_VERSION = '1.2.3'`.
    const defRe = /define\s*\(\s*['"]([A-Z0-9_]*_VERSION)['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g;
    const constRe = /const\s+([A-Z0-9_]*_VERSION)\s*=\s*['"]([^'"]+)['"]/g;
    for (const m of src.matchAll(defRe)) constants.push({ name: m[1], value: m[2] });
    for (const m of src.matchAll(constRe)) constants.push({ name: m[1], value: m[2] });
  }

  if (!header) {
    r.errors.push('Could not read header Version from the main plugin file.');
    return finalize(r, start);
  }
  r.notes.push(`Header Version: ${header}`);

  // readme.txt Stable tag.
  const readme = await readFileSafe(join(ctx.pluginDir, 'readme.txt'));
  if (readme) {
    const tag = stableTag(readme);
    if (!tag) {
      r.warnings.push('readme.txt present but no "Stable tag:" line found.');
    } else if (tag !== header) {
      r.errors.push(`readme.txt Stable tag "${tag}" != header Version "${header}".`);
    } else {
      r.notes.push(`readme Stable tag matches: ${tag}`);
    }
  } else {
    r.warnings.push('No readme.txt found.');
  }

  // *_VERSION constants.
  for (const c of constants) {
    if (c.value !== header) {
      r.errors.push(`Constant ${c.name} = "${c.value}" != header Version "${header}".`);
    }
  }
  if (constants.length > 0) {
    r.notes.push(`Checked ${constants.length} *_VERSION constant(s).`);
  }

  return finalize(r, start);
}

export const versionConsistencyGate: GateDefinition = {
  gate: 'versionConsistency',
  label: 'Version consistency',
  requiresDocker: false,
  run,
};
