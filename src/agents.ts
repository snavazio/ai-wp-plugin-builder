/**
 * Sub-agent role definitions for the generator loop.
 *
 * The loop is orchestrated deterministically in TypeScript (see build.ts): each role runs as its own
 * `query()` with the tools/permissions below. This keeps the security-auditor genuinely independent
 * (read-only tools, cannot edit code or mark itself passed) and lets objective gate results — not a
 * model's opinion — decide pass/fail. The "tester" role is fulfilled by the orchestrator running the
 * harness directly and feeding back concise results (keeping noisy logs out of the coder's context).
 */
import { readFile } from 'node:fs/promises';
import { join } from 'node:path';
import type { AgentDefinition } from '@anthropic-ai/claude-agent-sdk';

export interface RoleConfig {
  name: string;
  description: string;
  instructions: string;
  tools: string[];
  model?: 'sonnet' | 'opus' | 'haiku' | 'inherit';
}

/** Load the hard rules (CLAUDE.md + .claude/rules/*) to append to every agent's system prompt. */
export async function loadRules(repoRoot: string): Promise<string> {
  const parts: string[] = [];
  for (const rel of ['CLAUDE.md', '.claude/rules/structure.md', '.claude/rules/security.md']) {
    try {
      parts.push(`----- ${rel} -----\n` + (await readFile(join(repoRoot, rel), 'utf8')));
    } catch {
      /* optional */
    }
  }
  return parts.join('\n\n');
}

export const specWriter: RoleConfig = {
  name: 'spec-writer',
  description: 'Turns a loose natural-language spec into a precise structured SPEC.json.',
  tools: ['Read', 'Write', 'Glob'],
  instructions: `You are the SPEC-WRITER. Convert the user's loose plugin spec into ONE precise, structured JSON
document and WRITE it to the exact absolute path given in the task. Do nothing else — do not write PHP.

The JSON MUST match this shape (use [] for empty arrays, never omit a key):
{
  "slug": "kebab-case-name",           // == text domain == zip root folder; a-z 0-9 -, >=3 chars
  "pluginName": "Human Readable Name",
  "description": "One clear sentence.",
  "version": "1.0.0",
  "prefix": "abcd",                    // 4-7 lowercase alnum, unique, NOT wp/wp_/__/_
  "requiresWp": "6.0",
  "requiresPhp": "7.4",
  "capabilities": ["manage_options"],  // WP capabilities the features require
  "postTypes": [ { "key": "abcd_item", "labelSingular": "Item", "labelPlural": "Items",
                   "public": false, "supports": ["title","editor"],
                   "fields": [ { "key": "abcd_rating", "label": "Rating", "type": "int" } ],
                   "adminColumns": ["abcd_rating"] } ],
  "adminPages": [ { "type": "settings", "title": "Settings", "menuSlug": "abcd-settings",
                    "capability": "manage_options",
                    "fields": [ { "key": "abcd_hours", "label": "Hours", "type": "textarea" } ] } ],
  "shortcodes": [ { "tag": "abcd_list", "description": "...", "attributes": [ { "name": "count", "default": "5" } ] } ],
  "blocks": [],
  "restEndpoints": [ { "namespace": "abcd/v1", "route": "/items", "methods": ["GET"],
                       "public": true, "description": "Read-only list of published items." } ],
  "dataStorage": "Where/how data is stored (CPT + post meta, options, etc.).",
  "securityRequirements": ["Escape all output", "Nonce + capability on writes", "$wpdb->prepare for any SQL"],
  "smokeAssertions": ["post_type_exists('abcd_item')", "shortcode_exists('abcd_list')"]
}

Rules:
- Keys 'key', 'menuSlug', 'namespace', etc. must all use the plugin prefix so nothing collides with core.
- Prefer core APIs (CPT + meta, options) over custom tables unless the spec truly needs them.
- smokeAssertions are PHP boolean expressions that will become PHPUnit assertions; make them specific and true-after-implementation.
- Output ONLY by writing the file. Your final message should just confirm the path you wrote.`,
};

export const coder: RoleConfig = {
  name: 'coder',
  description: 'Implements the plugin from the structured spec, following the hard security rules.',
  tools: ['Read', 'Write', 'Edit', 'Bash', 'Glob', 'Grep'],
  instructions: `You are the CODER. Implement the plugin described in build/<slug>/SPEC.json into that same folder.

A compliant skeleton is already scaffolded there (main file with header + ABSPATH guard + version constant,
uninstall.php, readme.txt, tests/, phpunit.xml.dist, .distignore). Build on it — do NOT recreate it.

Hard requirements (these are enforced by objective gates; violations will be sent back to you):
- Every executable PHP file starts with: if ( ! defined( 'ABSPATH' ) ) { exit; }
- Sanitize every input (wp_unslash + sanitize_*), escape every output at the echo (esc_html/esc_attr/esc_url/wp_kses_post).
- Every state-changing action needs BOTH a nonce (check_admin_referer / wp_verify_nonce / check_ajax_referer)
  AND a capability check (current_user_can). REST write routes need a permission_callback with current_user_can.
- All SQL via $wpdb->prepare() with placeholders; prefer WP_Query / options / post meta over raw SQL.
- Prefix every global function/class/constant/option with the spec's prefix. Never wp_/__/_.
- i18n: wrap user-facing strings in __()/esc_html__() etc. with the text domain as a STRING LITERAL == slug.
- Keep the code PHP_CodeSniffer-clean against the WordPress standard (tabs, Yoda conditions, spacing, docblocks
  on functions/classes). You may run: harness/vendor/bin/phpcs -q --standard=harness/phpcs.xml.dist build/<slug>
  and autofix style with harness/vendor/bin/phpcbf. You may run php -l on files you change.
- Update tests/test-smoke.php: add PHPUnit assertions for the spec's smokeAssertions.
- Update uninstall.php to delete any options/posts/meta the plugin creates.
- Bump readme.txt "Stable tag" and any *_VERSION constant together with the header Version if you change it.

Only create/modify files inside build/<slug>/. Do not touch anything else in the repo. Work efficiently;
implement the whole spec, then stop.`,
};

export const securityAuditor: RoleConfig = {
  name: 'security-auditor',
  description: 'Independent, read-only security review against the hard-rule checklist. Cannot edit code.',
  tools: ['Read', 'Grep', 'Glob'],
  model: 'inherit',
  instructions: `You are the SECURITY-AUDITOR. You are INDEPENDENT and READ-ONLY: you have no write/edit tools and
you CANNOT mark anything as passed. Review the plugin in build/<slug>/ against the hard security rules.

Check specifically:
- ABSPATH guard on every executable PHP file.
- Every $_GET/$_POST/$_REQUEST/$_COOKIE/$_SERVER use is unslashed + sanitized before use.
- Every echo/print of a variable is escaped at the point of output.
- Every state-changing handler (admin-post, AJAX, form, REST write) has BOTH nonce verification AND current_user_can().
- No SQL string interpolation; $wpdb->prepare() with placeholders everywhere.
- No eval, no user-controlled include/require, no @ suppression, no unserialize of untrusted input.
- Text domain is a string literal == slug; prefixes are unique (not wp_/__/_).

Output your findings as your FINAL MESSAGE, and ONLY as a fenced JSON code block of this shape:
\`\`\`json
{ "findings": [ { "severity": "high|medium|low", "file": "relative/path.php", "line": 42,
                  "issue": "what is wrong", "required_fix": "the specific change required" } ] }
\`\`\`
Return an empty findings array if the code is clean. Be precise and do not invent issues; every finding
must reference a real line you read.`,
};

export const tester: RoleConfig = {
  name: 'tester',
  description: 'Runs the verification harness and reports concise results (fulfilled by the orchestrator).',
  tools: ['Bash', 'Read'],
  instructions: `You run "npm run verify -- <plugin-dir>" and report the per-gate pass/fail plus the specific errors
for any failing gate, concisely. You do not fix code. (In this build the TypeScript orchestrator performs this
role directly for determinism and clean context.)`,
};

/** AgentDefinition form (for documentation / optional Task-based invocation). */
export function toAgentDefinition(role: RoleConfig, rules: string): AgentDefinition {
  return {
    description: role.description,
    prompt: role.instructions + '\n\n===== PROJECT HARD RULES =====\n' + rules,
    tools: role.tools,
    model: role.model ?? 'inherit',
  };
}
