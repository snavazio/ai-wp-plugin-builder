/**
 * Spec coverage: confirm the code actually registers every feature the structured SPEC.json lists.
 *
 * The 8 gates judge whether code is safe and installs; none of them judges whether the code does what
 * the spec asked. A build whose coder step produced nothing therefore passes every gate (the scaffold is
 * valid). This check closes that hole cheaply and statically: if the spec lists admin pages, REST routes,
 * shortcodes, etc., a matching WordPress registration call must exist somewhere in the plugin's PHP.
 *
 * It is reported as an extra result alongside the gates; it does not change the 8-gate list.
 */
import { readFile } from 'node:fs/promises';
import { join, relative } from 'node:path';
import { findPhpFiles } from './util/exec.js';
import { emptyResult, finalize } from './gates/_util.js';
import type { GateResult, PipelineResult } from './types.js';

interface Check {
  key: string;
  label: string;
  /** A call that proves the feature is registered. */
  pattern: RegExp;
}

const CHECKS: Check[] = [
  { key: 'postTypes', label: 'custom post type', pattern: /\bregister_post_type\s*\(/ },
  { key: 'taxonomies', label: 'taxonomy', pattern: /\bregister_taxonomy\s*\(/ },
  { key: 'adminPages', label: 'admin page', pattern: /\badd_(?:menu|submenu|options|management|tools|dashboard|posts|media|pages|comments|theme|plugins|users|utility)_page\s*\(/ },
  { key: 'shortcodes', label: 'shortcode', pattern: /\badd_shortcode\s*\(/ },
  { key: 'blocks', label: 'block', pattern: /\bregister_block_type(?:_from_metadata)?\s*\(/ },
  // Custom routes, or core wp/v2 routes switched on through show_in_rest.
  { key: 'restEndpoints', label: 'REST route', pattern: /\bregister_rest_route\s*\(|['"]show_in_rest['"]\s*=>\s*true/ },
  { key: 'ajaxActions', label: 'AJAX action', pattern: /['"]wp_ajax_(?:nopriv_)?/ },
  { key: 'cronEvents', label: 'scheduled event', pattern: /\bwp_schedule_(?:single_)?event\s*\(/ },
  { key: 'widgets', label: 'widget', pattern: /\bregister_widget\s*\(|\bextends\s+WP_Widget\b|\bwp_add_dashboard_widget\s*\(/ },
];

/** Remove PHP comments so a feature "mentioned" in a TODO does not count as implemented. */
function stripComments(src: string): string {
  return src
    .replace(/\/\*[\s\S]*?\*\//g, ' ')
    .replace(/(^|[^:])\/\/[^\n]*/g, '$1')
    .replace(/^\s*#[^\n]*/gm, '');
}

export async function specCoverageResult(pluginDir: string): Promise<GateResult> {
  const start = Date.now();
  const r = emptyResult('specCoverage', 'Spec coverage (features implemented)', false);

  let spec: Record<string, unknown>;
  try {
    spec = JSON.parse(await readFile(join(pluginDir, 'SPEC.json'), 'utf8')) as Record<string, unknown>;
  } catch {
    r.notes.push('No readable SPEC.json — nothing to compare against.');
    return finalize(r, start);
  }

  let code = '';
  for (const f of await findPhpFiles(pluginDir)) {
    if (/(^|\/)tests\//.test(relative(pluginDir, f))) continue; // test files do not implement features
    code += '\n' + stripComments(await readFile(f, 'utf8').catch(() => ''));
  }

  let checked = 0;
  for (const c of CHECKS) {
    const listed = spec[c.key];
    if (!Array.isArray(listed) || listed.length === 0) continue;
    checked++;
    if (!c.pattern.test(code)) {
      r.errors.push(
        `The spec lists ${listed.length} ${c.label}(s) (${c.key}) but the code never registers one. Implement it.`,
      );
    }
  }
  r.notes.push(`Checked ${checked} feature type(s) from SPEC.json.`);
  return finalize(r, start);
}

/** Append the spec-coverage result to a pipeline result, and fold it into the overall verdict. */
export async function withSpecCoverage(pipe: PipelineResult, pluginDir: string): Promise<PipelineResult> {
  const sc = await specCoverageResult(pluginDir);
  process.stdout.write(`    [${sc.passed ? 'PASS' : 'FAIL'}] ${sc.label}\n`);
  return { ...pipe, results: [...pipe.results, sc], passed: pipe.passed && sc.passed };
}
