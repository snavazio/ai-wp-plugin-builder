/**
 * Minimal Ollama client (chat + embeddings) over its HTTP API, using global fetch.
 *
 * Configurable via env so you can point at a remote Ollama (e.g. an Olares box):
 *   OLLAMA_HOST        default http://127.0.0.1:11434
 *   OLLAMA_MODEL       default qwen2.5-coder:14b   (the code-generation model)
 *   OLLAMA_EMBED_MODEL default nomic-embed-text    (RAG embeddings)
 */

export interface OllamaConfig {
  host: string;
  model: string;
  embedModel: string;
}

export function ollamaConfig(): OllamaConfig {
  return {
    host: (process.env.OLLAMA_HOST || 'http://127.0.0.1:11434').replace(/\/$/, ''),
    model: process.env.OLLAMA_MODEL || 'qwen2.5-coder:14b',
    embedModel: process.env.OLLAMA_EMBED_MODEL || 'nomic-embed-text',
  };
}

export interface ChatMessage {
  role: 'system' | 'user' | 'assistant';
  content: string;
}

/** Check that the Ollama server is reachable and the model is present. */
export async function ollamaHealth(cfg: OllamaConfig): Promise<{ ok: boolean; message: string }> {
  try {
    const res = await fetch(`${cfg.host}/api/tags`, { signal: AbortSignal.timeout(5000) });
    if (!res.ok) return { ok: false, message: `Ollama /api/tags returned ${res.status}` };
    const data = (await res.json()) as { models?: Array<{ name: string }> };
    const names = (data.models ?? []).map((m) => m.name);
    const have = names.some((n) => n === cfg.model || n.split(':')[0] === cfg.model.split(':')[0]);
    if (!have) return { ok: false, message: `Model "${cfg.model}" not found. Available: ${names.join(', ') || '(none)'}` };
    return { ok: true, message: `Ollama up at ${cfg.host}; model ${cfg.model} present.` };
  } catch (e) {
    return { ok: false, message: `Cannot reach Ollama at ${cfg.host}: ${String(e)}` };
  }
}

export interface ChatResult {
  content: string;
  promptTokens: number;
  completionTokens: number;
  durationMs: number;
}

export async function ollamaChat(
  cfg: OllamaConfig,
  messages: ChatMessage[],
  opts: { temperature?: number; numCtx?: number; timeoutMs?: number } = {},
): Promise<ChatResult> {
  const start = Date.now();
  const res = await fetch(`${cfg.host}/api/chat`, {
    method: 'POST',
    headers: { 'content-type': 'application/json' },
    body: JSON.stringify({
      model: cfg.model,
      messages,
      stream: false,
      options: {
        temperature: opts.temperature ?? 0.1,
        num_ctx: opts.numCtx ?? 16384,
      },
    }),
    redirect: 'error',
    signal: AbortSignal.timeout(opts.timeoutMs ?? 15 * 60_000),
  });
  if (!res.ok) {
    console.error(`[ollama] chat failed: ${res.status} ${(await res.text()).slice(0, 500)}`);
    throw new Error(`Ollama chat failed: HTTP ${res.status} (details are in the builder service log).`);
  }
  const data = (await res.json()) as {
    message?: { content?: string };
    prompt_eval_count?: number;
    eval_count?: number;
  };
  return {
    content: data.message?.content ?? '',
    promptTokens: data.prompt_eval_count ?? 0,
    completionTokens: data.eval_count ?? 0,
    durationMs: Date.now() - start,
  };
}

export async function ollamaEmbed(cfg: OllamaConfig, text: string): Promise<number[]> {
  const res = await fetch(`${cfg.host}/api/embeddings`, {
    method: 'POST',
    headers: { 'content-type': 'application/json' },
    body: JSON.stringify({ model: cfg.embedModel, prompt: text }),
    redirect: 'error',
    signal: AbortSignal.timeout(60_000),
  });
  if (!res.ok) {
    console.error(`[ollama] embeddings failed: ${res.status} ${(await res.text()).slice(0, 500)}`);
    throw new Error(`Ollama embeddings failed: HTTP ${res.status} (details are in the builder service log).`);
  }
  const data = (await res.json()) as { embedding?: number[] };
  return data.embedding ?? [];
}
