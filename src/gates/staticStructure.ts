/**
 * Gate 2 — staticStructure: cheap regex/text checks that don't need any tooling.
 *   - ABSPATH guard present in every executable PHP file (uninstall.php uses WP_UNINSTALL_PLUGIN guard)
 *   - a valid plugin header in the main file with all required fields
 *   - Text Domain == folder slug
 *   - uninstall.php guard present if the file exists
 */
import type { GateContext, GateDefinition, GateResult } from '../types.js';
import { findPhpFiles } from '../util/exec.js';
import { readFile } from 'node:fs/promises';
import { basename, relative } from 'node:path';
import { emptyResult, finalize } from './_util.js';

const ABSPATH_GUARD = /!\s*defined\s*\(\s*['"]ABSPATH['"]\s*\)/;
const UNINSTALL_GUARD = /!\s*defined\s*\(\s*['"]WP_UNINSTALL_PLUGIN['"]\s*\)/;

/**
 * Strip PHP comments so a guard "mentioned" in a comment doesn't count as the real thing.
 * Heuristic (good enough for guard detection): remove block, line, and hash comments.
 * Over-stripping can only cause a false "missing guard", never a false pass.
 */
function stripComments(src: string): string {
  return src
    .replace(/\/\*[\s\S]*?\*\//g, ' ') // block comments (incl. docblocks)
    .replace(/(^|[^:])\/\/[^\n]*/g, '$1') // line comments (avoid eating http://)
    .replace(/^\s*#[^\n]*/gm, ''); // hash comments
}

const REQUIRED_HEADERS = [
  'Plugin Name',
  'Version',
  'License',
  'Text Domain',
  'Requires at least',
  'Requires PHP',
];

function parseHeaderField(src: string, field: string): string | null {
  const re = new RegExp(`^[ \\t/*#@]*${field.replace(/ /g, '[ \\t]')}\\s*:\\s*(.+)$`, 'im');
  const m = src.match(re);
  return m ? m[1].trim().replace(/\*+\/?\s*$/, '').trim() : null;
}

async function run(ctx: GateContext): Promise<GateResult> {
  const start = Date.now();
  const r = emptyResult('staticStructure', 'Static structure & header checks', false);
  const files = await findPhpFiles(ctx.pluginDir);

  // 1. ABSPATH / uninstall guards on every PHP file (test files are exempt — they run under PHPUnit).
  for (const file of files) {
    const rel = relative(ctx.pluginDir, file);
    if (/(^|\/)tests\//.test(rel)) {
      continue;
    }
    const src = await readFile(file, 'utf8');
    // Guard must be real code, not merely mentioned in a comment.
    const code = stripComments(src);
    if (basename(file) === 'uninstall.php') {
      if (!UNINSTALL_GUARD.test(code)) {
        r.errors.push(`${rel}: missing "if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) die;" guard.`);
      }
      continue;
    }
    if (!ABSPATH_GUARD.test(code)) {
      r.errors.push(`${rel}: missing "if ( ! defined( 'ABSPATH' ) ) { exit; }" direct-access guard.`);
    }
  }

  // 2. Locate the main plugin file (root-level .php containing "Plugin Name:").
  let mainFile: string | null = null;
  let mainSrc = '';
  for (const file of files) {
    const src = await readFile(file, 'utf8');
    if (/^[ \t/*#@]*Plugin Name\s*:/im.test(src)) {
      mainFile = file;
      mainSrc = src;
      break;
    }
  }
  if (!mainFile) {
    r.errors.push('No main plugin file with a "Plugin Name:" header found.');
    return finalize(r, start);
  }
  r.notes.push(`Main file: ${relative(ctx.pluginDir, mainFile)}`);

  // 3. Required header fields present.
  const values: Record<string, string | null> = {};
  for (const field of REQUIRED_HEADERS) {
    const v = parseHeaderField(mainSrc, field);
    values[field] = v;
    if (!v) r.errors.push(`Plugin header missing required field: "${field}".`);
  }

  // 4. License must be GPLv2+.
  const license = values['License'] ?? '';
  if (license && !/gpl\s*v?2|gpl-2|gnu general public license/i.test(license)) {
    r.warnings.push(`License "${license}" is not clearly GPLv2-or-later.`);
  }

  // 5. Text Domain must equal folder slug.
  const textDomain = values['Text Domain'];
  if (textDomain && textDomain !== ctx.slug) {
    r.errors.push(`Text Domain "${textDomain}" does not equal folder slug "${ctx.slug}".`);
  }

  return finalize(r, start);
}

export const staticStructureGate: GateDefinition = {
  gate: 'staticStructure',
  label: 'Static structure & header checks',
  requiresDocker: false,
  run,
};
