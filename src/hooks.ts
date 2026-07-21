/**
 * SDK hooks for the coder agent.
 *
 * PreToolUse security-pattern gate: block Write/Edit operations that echo unescaped superglobals
 * or interpolate variables into SQL, and block any write outside the plugin workspace. The coder
 * sees the denial reason and fixes inline before the code ever reaches the harness.
 *
 * PostToolUse: lightweight accounting (the TS orchestrator runs the harness itself — the "tester").
 */
import { resolve, sep } from 'node:path';
import type { HookEvent, HookCallbackMatcher, PreToolUseHookInput, SyncHookJSONOutput } from '@anthropic-ai/claude-agent-sdk';

export interface HookStats {
  securityBlocks: number;
  boundaryBlocks: number;
  reasons: string[];
}

export function newHookStats(): HookStats {
  return { securityBlocks: 0, boundaryBlocks: 0, reasons: [] };
}

/** Detect an echo/print of a superglobal that is not run through an escaping/casting function. */
function unescapedSuperglobalOutput(text: string): string | null {
  const lines = text.split('\n');
  for (const line of lines) {
    const m = /\b(?:echo|print)\b[^;]*\$_(GET|POST|REQUEST|COOKIE|SERVER)\b/.exec(line);
    if (!m) continue;
    const safe = /(esc_html|esc_attr|esc_url|esc_textarea|wp_kses|absint|intval|\(\s*int\s*\)|sanitize_)/.test(line);
    if (!safe) return `Unescaped output of $_${m[1]} in: ${line.trim().slice(0, 120)}`;
  }
  return null;
}

/** Detect a variable interpolated into a $wpdb query string (SQL injection shape). */
function interpolatedSql(text: string): string | null {
  // $wpdb->query( "... $var ..." ) or "...{$var}..." — prepare() with placeholders is fine.
  const re = /\$wpdb->(query|get_results|get_var|get_row|get_col)\s*\(\s*(["'])(?:(?!\2).)*?\$\{?[A-Za-z_]/;
  const m = re.exec(text);
  if (m) return `Interpolated variable in $wpdb->${m[1]}() — use $wpdb->prepare() with placeholders.`;
  return null;
}

function deny(reason: string): SyncHookJSONOutput {
  return {
    continue: true,
    hookSpecificOutput: {
      hookEventName: 'PreToolUse',
      permissionDecision: 'deny',
      permissionDecisionReason: reason,
    },
  };
}

function within(child: string, parent: string): boolean {
  const c = resolve(child);
  const p = resolve(parent);
  return c === p || c.startsWith(p + sep);
}

export function makeCoderHooks(
  workspaceDir: string,
  distDir: string,
  stats: HookStats,
): Partial<Record<HookEvent, HookCallbackMatcher[]>> {
  const preToolUse: HookCallbackMatcher = {
    hooks: [
      async (input) => {
        if (input.hook_event_name !== 'PreToolUse') return { continue: true };
        const { tool_name, tool_input } = input as PreToolUseHookInput;
        const ti = (tool_input ?? {}) as Record<string, unknown>;

        if (tool_name === 'Write' || tool_name === 'Edit') {
          const filePath = typeof ti.file_path === 'string' ? ti.file_path : '';
          // Boundary: writes must stay inside the plugin workspace (or dist).
          if (filePath && !within(filePath, workspaceDir) && !within(filePath, distDir)) {
            stats.boundaryBlocks++;
            const r = `Write blocked: ${filePath} is outside the plugin workspace (${workspaceDir}).`;
            stats.reasons.push(r);
            return deny(r);
          }
          // Security patterns on the text being written.
          const text =
            tool_name === 'Write'
              ? String(ti.content ?? '')
              : String(ti.new_string ?? '') + '\n' + String(ti.replace_all ?? '');
          const sec = unescapedSuperglobalOutput(text) ?? interpolatedSql(text);
          if (sec) {
            stats.securityBlocks++;
            const r = `Security gate: ${sec} Fix before writing (sanitize input, escape output, use $wpdb->prepare()).`;
            stats.reasons.push(r);
            return deny(r);
          }
        }

        if (tool_name === 'Bash') {
          const cmd = String(ti.command ?? '');
          if (/\brm\s+-rf?\s+\/(?:\s|$)|\brm\s+-rf?\s+~|\bgit\s+push\b|\bsudo\b/.test(cmd)) {
            stats.boundaryBlocks++;
            const r = `Bash blocked (out-of-scope/destructive): ${cmd.slice(0, 120)}`;
            stats.reasons.push(r);
            return deny(r);
          }
        }

        return { continue: true };
      },
    ],
  };

  return { PreToolUse: [preToolUse] };
}
