/**
 * Price labels for the model drop-down in the WordPress Settings screen.
 *
 * Model-list APIs do not return prices (except OpenRouter's), so:
 *   - Claude: Anthropic first-party API list prices, USD per 1M tokens, input / output.
 *     Source: Anthropic's model reference, cached 2026-10-06. Update this table when prices change.
 *   - OpenRouter: read live from its /models response (per-token prices).
 *   - Models on your own hardware (Ollama, or an OpenAI-compatible server on a private/LAN host): free.
 *   - Other cloud platforms (OpenAI, Gemini, Groq, …): no label — their APIs do not publish prices.
 */

/** [input, output] USD per 1M tokens. */
const CLAUDE: Record<string, [number, number]> = {
  'claude-fable-5-1': [10, 50],
  'claude-fable-5': [10, 50],
  'claude-mythos-5-1': [10, 50],
  'claude-opus-5-5': [4, 20],
  'claude-opus-5': [5, 25],
  'claude-opus-4-8': [5, 25],
  'claude-opus-4-7': [5, 25],
  'claude-opus-4-6': [5, 25],
  'claude-sonnet-5-5': [2, 10],
  'claude-sonnet-5': [2, 10],
  'claude-sonnet-4-6': [3, 15],
  'claude-haiku-5-5': [0.1, 0.5],
  'claude-haiku-4-5': [1, 5],
};

/** Notes that change how a price reads. */
const CLAUDE_NOTE: Record<string, string> = {
  'claude-haiku-5-5': ' (prompts ≤100K tokens; $0.50 / $2.50 above)',
};

function money(n: number): string {
  if (n === 0) return '$0';
  if (n < 1) return `$${n.toFixed(2)}`;
  return Number.isInteger(n) ? `$${n}` : `$${n.toFixed(2)}`;
}

function perMillion(input: number, output: number): string {
  return `${money(input)} / ${money(output)} per 1M tokens`;
}

/** Strip a trailing -YYYYMMDD snapshot date so dated IDs match the table. */
function baseClaudeId(id: string): string {
  return id.replace(/-\d{8}$/, '');
}

export function claudePrice(id: string): string {
  const p = CLAUDE[id] ?? CLAUDE[baseClaudeId(id)];
  if (!p) return 'price not listed';
  return perMillion(p[0], p[1]) + (CLAUDE_NOTE[id] ?? CLAUDE_NOTE[baseClaudeId(id)] ?? '');
}

/** True for hosts that are this machine or the local/private network (your own hardware). */
export function isPrivateHost(baseUrl: string): boolean {
  let host = '';
  try {
    host = new URL(baseUrl).hostname.toLowerCase().replace(/^\[|\]$/g, '');
  } catch {
    return false;
  }
  if (host === 'localhost' || host.endsWith('.local') || host.endsWith('.lan') || host.endsWith('.internal')) return true;
  if (!host.includes('.') && !host.includes(':')) return true; // single-label LAN name, e.g. "thing2"
  if (host === '::1' || host.startsWith('fc') || host.startsWith('fd')) return true;
  const m = host.match(/^(\d+)\.(\d+)\.\d+\.\d+$/);
  if (!m) return false;
  const [a, b] = [Number(m[1]), Number(m[2])];
  return a === 127 || a === 10 || (a === 192 && b === 168) || (a === 172 && b >= 16 && b <= 31) || (a === 100 && b >= 64 && b <= 127);
}

export const FREE_LOCAL = 'free (your own hardware)';

/** OpenRouter returns per-token USD strings; convert to a per-1M label. */
export function openrouterPrice(pricing: { prompt?: string; completion?: string } | undefined): string | null {
  if (!pricing) return null;
  const i = Number(pricing.prompt);
  const o = Number(pricing.completion);
  if (!Number.isFinite(i) || !Number.isFinite(o)) return null;
  if (i === 0 && o === 0) return 'free';
  return perMillion(i * 1e6, o * 1e6);
}
