/**
 * Protocol-agnostic LLM client: one chat() and one test() over the four wire protocols the builder
 * supports. Nearly every new AI platform speaks one of these (most are OpenAI-compatible), so a new
 * platform is a new base URL + key + model, not new code.
 *
 *   anthropic  Messages API            (Claude)
 *   ollama     /api/chat, /api/tags     (local or remote Ollama)
 *   openai     /chat/completions        (OpenAI, OpenRouter, Groq, DeepSeek, Mistral, xAI, LM Studio, vLLM…)
 *   gemini     :generateContent         (Google Gemini)
 *
 * A platform arrives either in a service request body (the WordPress plugin's saved AI list) or, for the
 * CLI/child processes, from env: AIWPB_LLM_PROTOCOL / _BASE_URL / _API_KEY / _MODEL. With none set it
 * falls back to Ollama via OLLAMA_HOST / OLLAMA_MODEL, so existing CLI usage is unchanged.
 */
import { ollamaConfig, ollamaChat, type ChatMessage } from './ollama.js';

export type { ChatMessage };

export const PROTOCOLS = ['anthropic', 'ollama', 'openai', 'gemini'] as const;
export type Protocol = (typeof PROTOCOLS)[number];

export interface Platform {
  protocol: Protocol;
  baseUrl: string;
  apiKey: string;
  model: string;
}

export interface LlmResult {
  content: string;
  promptTokens: number;
  completionTokens: number;
}

export interface TestResult {
  ok: boolean;
  message: string;
  ms: number;
  models?: string[];
}

const DEFAULT_BASE: Record<Protocol, string> = {
  anthropic: 'https://api.anthropic.com',
  ollama: 'http://127.0.0.1:11434',
  openai: 'https://api.openai.com/v1',
  gemini: 'https://generativelanguage.googleapis.com/v1beta',
};

function base(p: Platform): string {
  return (p.baseUrl || DEFAULT_BASE[p.protocol]).replace(/\/+$/, '');
}

function anthropicKey(p: Platform): string {
  return p.apiKey || process.env.ANTHROPIC_API_KEY || '';
}

/** Validate an untrusted platform object (from a request body). Returns null if unusable. */
export function parsePlatform(raw: unknown): Platform | null {
  if (!raw || typeof raw !== 'object') return null;
  const r = raw as Record<string, unknown>;
  const protocol = String(r.protocol ?? '') as Protocol;
  if (!PROTOCOLS.includes(protocol)) return null;
  const baseUrl = typeof r.baseUrl === 'string' ? r.baseUrl.trim() : '';
  if (baseUrl && !/^https?:\/\//i.test(baseUrl)) return null;
  return {
    protocol,
    baseUrl,
    apiKey: typeof r.apiKey === 'string' ? r.apiKey.trim() : '',
    model: typeof r.model === 'string' ? r.model.trim() : '',
  };
}

/** The platform a child build process should use (set by the service via env). */
export function platformFromEnv(): Platform {
  const protocol = process.env.AIWPB_LLM_PROTOCOL as Protocol | undefined;
  if (protocol && PROTOCOLS.includes(protocol) && protocol !== 'ollama') {
    return {
      protocol,
      baseUrl: process.env.AIWPB_LLM_BASE_URL || '',
      apiKey: process.env.AIWPB_LLM_API_KEY || '',
      model: process.env.AIWPB_LLM_MODEL || '',
    };
  }
  const o = ollamaConfig();
  return { protocol: 'ollama', baseUrl: o.host, apiKey: '', model: o.model };
}

/** Env vars that hand a platform to a child process (inverse of platformFromEnv). */
export function platformEnv(p: Platform): NodeJS.ProcessEnv {
  if (p.protocol === 'anthropic') {
    const env: NodeJS.ProcessEnv = {};
    if (p.apiKey) env.ANTHROPIC_API_KEY = p.apiKey;
    if (p.model) env.AIWPB_CLAUDE_MODEL = p.model;
    if (p.baseUrl && base(p) !== DEFAULT_BASE.anthropic) env.ANTHROPIC_BASE_URL = base(p);
    return env;
  }
  if (p.protocol === 'ollama') {
    return { OLLAMA_HOST: base(p), ...(p.model ? { OLLAMA_MODEL: p.model } : {}) };
  }
  return {
    AIWPB_LLM_PROTOCOL: p.protocol,
    AIWPB_LLM_BASE_URL: base(p),
    AIWPB_LLM_API_KEY: p.apiKey,
    AIWPB_LLM_MODEL: p.model,
  };
}

async function getJson(url: string, headers: Record<string, string>, timeoutMs: number): Promise<{ status: number; data: unknown; text: string }> {
  const res = await fetch(url, { headers, signal: AbortSignal.timeout(timeoutMs) });
  const text = await res.text();
  let data: unknown = null;
  try {
    data = JSON.parse(text);
  } catch {
    /* non-JSON body */
  }
  return { status: res.status, data, text };
}

async function postJson(url: string, headers: Record<string, string>, body: unknown, timeoutMs: number): Promise<unknown> {
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'content-type': 'application/json', ...headers },
    body: JSON.stringify(body),
    signal: AbortSignal.timeout(timeoutMs),
  });
  const text = await res.text();
  if (!res.ok) throw new Error(`${res.status} ${text.slice(0, 500)}`);
  return JSON.parse(text);
}

function errMessage(data: unknown, text: string): string {
  const d = data as { error?: { message?: string } | string; message?: string } | null;
  const e = d?.error;
  return (typeof e === 'string' ? e : e?.message) || d?.message || text.slice(0, 200) || 'no details';
}

/** One chat completion. Throws on transport/HTTP errors. */
export async function llmChat(
  p: Platform,
  messages: ChatMessage[],
  opts: { temperature?: number; numCtx?: number; timeoutMs?: number; maxTokens?: number } = {},
): Promise<LlmResult> {
  const timeoutMs = opts.timeoutMs ?? 15 * 60_000;
  const temperature = opts.temperature ?? 0.1;
  const maxTokens = opts.maxTokens ?? 16000;
  const system = messages.filter((m) => m.role === 'system').map((m) => m.content).join('\n\n');
  const turns = messages.filter((m) => m.role !== 'system');

  if (!p.model && p.protocol !== 'ollama') throw new Error(`No model set for this ${p.protocol} platform.`);

  switch (p.protocol) {
    case 'ollama': {
      const o = ollamaConfig();
      const r = await ollamaChat({ ...o, host: base(p), model: p.model || o.model }, messages, { temperature, numCtx: opts.numCtx, timeoutMs });
      return { content: r.content, promptTokens: r.promptTokens, completionTokens: r.completionTokens };
    }
    case 'openai': {
      const data = (await postJson(
        `${base(p)}/chat/completions`,
        p.apiKey ? { authorization: `Bearer ${p.apiKey}` } : {},
        { model: p.model, messages, temperature, stream: false },
        timeoutMs,
      )) as { choices?: Array<{ message?: { content?: string } }>; usage?: { prompt_tokens?: number; completion_tokens?: number } };
      return {
        content: data.choices?.[0]?.message?.content ?? '',
        promptTokens: data.usage?.prompt_tokens ?? 0,
        completionTokens: data.usage?.completion_tokens ?? 0,
      };
    }
    case 'anthropic': {
      const key = anthropicKey(p);
      if (!key) throw new Error('No Anthropic API key: save one on this platform, or set ANTHROPIC_API_KEY on the builder service.');
      const data = (await postJson(
        `${base(p)}/v1/messages`,
        { 'x-api-key': key, 'anthropic-version': '2023-06-01' },
        // No temperature: current Claude models reject it as deprecated.
        { model: p.model, max_tokens: maxTokens, ...(system ? { system } : {}), messages: turns },
        timeoutMs,
      )) as { content?: Array<{ type: string; text?: string }>; usage?: { input_tokens?: number; output_tokens?: number } };
      return {
        content: (data.content ?? []).filter((b) => b.type === 'text').map((b) => b.text ?? '').join(''),
        promptTokens: data.usage?.input_tokens ?? 0,
        completionTokens: data.usage?.output_tokens ?? 0,
      };
    }
    case 'gemini': {
      const model = p.model.replace(/^models\//, '');
      const data = (await postJson(
        `${base(p)}/models/${encodeURIComponent(model)}:generateContent`,
        { 'x-goog-api-key': p.apiKey },
        {
          ...(system ? { systemInstruction: { parts: [{ text: system }] } } : {}),
          contents: turns.map((m) => ({ role: m.role === 'assistant' ? 'model' : 'user', parts: [{ text: m.content }] })),
          generationConfig: { temperature, maxOutputTokens: maxTokens },
        },
        timeoutMs,
      )) as {
        candidates?: Array<{ content?: { parts?: Array<{ text?: string }> } }>;
        usageMetadata?: { promptTokenCount?: number; candidatesTokenCount?: number };
      };
      return {
        content: (data.candidates?.[0]?.content?.parts ?? []).map((x) => x.text ?? '').join(''),
        promptTokens: data.usageMetadata?.promptTokenCount ?? 0,
        completionTokens: data.usageMetadata?.candidatesTokenCount ?? 0,
      };
    }
  }
}

/**
 * Connection test: proves the endpoint answers, the key is accepted, and the model exists — using each
 * platform's free model-listing/lookup call, so a test costs nothing and doesn't load a large model.
 */
export async function testPlatform(p: Platform, timeoutMs = 15_000): Promise<TestResult> {
  const start = Date.now();
  const done = (ok: boolean, message: string, models?: string[]): TestResult => ({ ok, message, ms: Date.now() - start, models });
  const b = base(p);
  try {
    switch (p.protocol) {
      case 'ollama': {
        const r = await getJson(`${b}/api/tags`, {}, timeoutMs);
        if (r.status !== 200) return done(false, `Ollama at ${b} answered HTTP ${r.status}.`);
        const names = ((r.data as { models?: Array<{ name: string }> })?.models ?? []).map((m) => m.name);
        if (!p.model) return done(true, `Ollama reachable at ${b} (${names.length} models). No model set.`, names);
        const have = names.some((n) => n === p.model || n === `${p.model}:latest`);
        return have
          ? done(true, `Connected to Ollama at ${b}; model ${p.model} is installed.`, names)
          : done(false, `Ollama is reachable, but model "${p.model}" is not installed there. Run: ollama pull ${p.model}`, names);
      }
      case 'openai': {
        const r = await getJson(`${b}/models`, p.apiKey ? { authorization: `Bearer ${p.apiKey}` } : {}, timeoutMs);
        if (r.status === 401 || r.status === 403) return done(false, `Key rejected (HTTP ${r.status}): ${errMessage(r.data, r.text)}`);
        if (r.status !== 200) return done(false, `${b}/models answered HTTP ${r.status}: ${errMessage(r.data, r.text)}`);
        const ids = ((r.data as { data?: Array<{ id: string }> })?.data ?? []).map((m) => m.id);
        if (!p.model) return done(true, `Connected to ${b} (${ids.length} models). No model set.`, ids);
        if (ids.length === 0 || ids.includes(p.model)) return done(true, `Connected to ${b}; model ${p.model} is available.`, ids);
        return done(false, `Connected, but model "${p.model}" is not offered by ${b}.`, ids);
      }
      case 'anthropic': {
        const key = anthropicKey(p);
        if (!key) return done(false, 'No API key saved, and ANTHROPIC_API_KEY is not set on the builder service.');
        const headers = { 'x-api-key': key, 'anthropic-version': '2023-06-01' };
        const path = p.model ? `/v1/models/${encodeURIComponent(p.model)}` : '/v1/models';
        const r = await getJson(`${b}${path}`, headers, timeoutMs);
        if (r.status === 401 || r.status === 403) return done(false, `Key rejected (HTTP ${r.status}): ${errMessage(r.data, r.text)}`);
        if (r.status === 404 && p.model) return done(false, `Key works, but model "${p.model}" was not found.`);
        if (r.status !== 200) return done(false, `Anthropic answered HTTP ${r.status}: ${errMessage(r.data, r.text)}`);
        const via = p.apiKey ? 'saved key' : "the service's ANTHROPIC_API_KEY";
        return done(true, p.model ? `Connected to Anthropic (${via}); model ${p.model} is available.` : `Connected to Anthropic (${via}). No model set.`);
      }
      case 'gemini': {
        if (!p.apiKey) return done(false, 'No API key saved for this Gemini platform.');
        const model = p.model.replace(/^models\//, '');
        const path = model ? `/models/${encodeURIComponent(model)}` : '/models';
        const r = await getJson(`${b}${path}`, { 'x-goog-api-key': p.apiKey }, timeoutMs);
        if (r.status === 400 || r.status === 401 || r.status === 403) return done(false, `Key rejected (HTTP ${r.status}): ${errMessage(r.data, r.text)}`);
        if (r.status === 404 && model) return done(false, `Key works, but model "${model}" was not found.`);
        if (r.status !== 200) return done(false, `Gemini answered HTTP ${r.status}: ${errMessage(r.data, r.text)}`);
        return done(true, model ? `Connected to Gemini; model ${model} is available.` : 'Connected to Gemini. No model set.');
      }
    }
  } catch (e) {
    const msg = e instanceof Error ? (e.cause instanceof Error ? `${e.message} (${e.cause.message})` : e.message) : String(e);
    return done(false, `Could not reach ${b}: ${msg}`);
  }
}
