/**
 * Builder service — wraps the CLI generator as a small authenticated HTTP API so the companion
 * WordPress plugin (or any client) can submit a spec and receive a verified .zip.
 *
 *   AIWPB_API_KEY=<secret> npm run serve            # default port 8787
 *   AIWPB_PORT=9000 npm run serve
 *
 * Endpoints (all under /api, key required via X-API-Key except /api/health):
 *   GET  /api/health                            -> { ok, service, engines, protocols }
 *   POST /api/chat           {messages, platform?}   -> { reply, model }
 *   POST /api/build          {spec, platform?}       -> { jobId }
 *   POST /api/ingest         {zipB64, spec, platform?} -> { jobId }
 *   POST /api/platforms/test {platform}              -> { ok, message, ms, models? }
 *   GET  /api/jobs/:id                          -> { id, status, engine, log[], artifact? }
 *   GET  /api/jobs/:id/zip                      -> streams the built .zip
 *
 * `platform` = { protocol: anthropic|ollama|openai|gemini, baseUrl, apiKey, model } — one entry from the
 * WordPress plugin's AI list. Anthropic builds run on the Claude Agent SDK; every other protocol runs the
 * file-protocol loop (build-local). Without `platform`, the legacy `engine` + `model` fields still work.
 *
 * Builds are SERIALIZED (one at a time) because they share the wp-env sandbox + repo-root .wp-env.json.
 */
import http from 'node:http';
import { randomUUID, randomBytes } from 'node:crypto';
import { spawn } from 'node:child_process';
import { mkdir, writeFile, readFile, access } from 'node:fs/promises';
import { createReadStream } from 'node:fs';
import { join, basename } from 'node:path';
import type { BuildResult } from './resultFile.js';
import { ollamaConfig } from './engines/ollama.js';
import { PROTOCOLS, allowedHosts, llmChat, parsePlatform, platformEnv, testPlatform, type ChatMessage, type Platform } from './engines/llm.js';

const CHAT_SYSTEM = `You are a friendly WordPress plugin consultant helping a user define a plugin to build.
Talk like a person in a chat: reply in 1-3 short sentences and ask at most one or two questions at a time.
Do NOT use headings, bold labels, or bulleted forms. Draw out, over the conversation, what matters for the
plugin: purpose, custom post types + fields, taxonomies, admin screens/columns, shortcodes/blocks, REST
endpoints, AJAX, cron, widgets, capabilities, and data cleanup on uninstall — but only ask about what is
actually ambiguous or missing, a bit at a time. Do NOT write PHP or code. Assume standard WordPress security
(escaping, sanitizing, nonces, capability checks) is always applied — never ask about it. Once you have
enough for a solid plugin, give a one or two sentence summary of what you'll build and tell the user they can
click the "Build plugin" button.`;

const DISTILL_INSTRUCTION = `Write the FINAL plugin specification as a single clear description in plain
English that captures every decision from our conversation (name, post types + fields, taxonomies, admin
screens/columns, shortcodes/blocks, REST endpoints, AJAX, cron, widgets, capabilities, data cleanup). Output
ONLY the specification text — no preamble, no questions, no markdown headings.`;

/** Strip qwen3-style <think>…</think> reasoning from a reply. */
function stripThink(s: string): string {
  return s.replace(/<think>[\s\S]*?<\/think>/gi, '').trim();
}

interface ServeEnv {
  repoRoot: string;
  harnessDir: string;
}

type JobStatus = 'queued' | 'running' | 'done' | 'error';

interface Job {
  id: string;
  engine: 'claude' | 'local';
  status: JobStatus;
  createdAt: number;
  log: string[];
  artifact?: BuildResult;
  error?: string;
}

/** How a job hands its engine to the child process: which engine runs, plus the env that configures it. */
interface EngineSpec {
  engine: 'claude' | 'local';
  env: NodeJS.ProcessEnv;
}

const MAX_LOG_LINES = 800;

/** Resolve a request body to an engine: a `platform` object wins; otherwise the legacy engine/model fields. */
function engineFromBody(body: Record<string, unknown> | null): EngineSpec | { error: string } {
  if (body?.platform !== undefined) {
    const p = parsePlatform(body.platform);
    if (!p) return { error: `Invalid "platform" (protocol must be one of ${PROTOCOLS.join(', ')}; baseUrl must be http(s)).` };
    return { engine: p.protocol === 'anthropic' ? 'claude' : 'local', env: platformEnv(p) };
  }
  const engine: 'claude' | 'local' = body?.engine === 'local' ? 'local' : 'claude';
  const model = typeof body?.model === 'string' ? body.model : '';
  return { engine, env: engine === 'local' && model ? { OLLAMA_MODEL: model } : {} };
}

export async function runServer(_args: string[], env: ServeEnv): Promise<number> {
  const port = Number(process.env.AIWPB_PORT || 8787);
  const apiKey = process.env.AIWPB_API_KEY || randomBytes(24).toString('hex');
  const generatedKey = !process.env.AIWPB_API_KEY;
  const jobsRoot = join(env.repoRoot, '.builder-jobs');
  await mkdir(jobsRoot, { recursive: true });

  const jobs = new Map<string, Job>();
  let chain: Promise<void> = Promise.resolve(); // serialize builds

  function enqueue(job: Job, specText: string, es: EngineSpec): void {
    jobs.set(job.id, job);
    chain = chain.then(() => runJob(env, jobsRoot, job, specText, es));
  }

  function enqueueIngest(job: Job, zipB64: string, changeRequest: string, es: EngineSpec): void {
    jobs.set(job.id, job);
    chain = chain.then(() => runIngestJob(env, jobsRoot, job, zipB64, changeRequest, es));
  }

  const server = http.createServer(async (req, res) => {
    const url = new URL(req.url || '/', `http://localhost`);
    const path = url.pathname;
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, X-API-Key');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    if (req.method === 'OPTIONS') return end(res, 204, '');

    if (path === '/api/health') {
      return json(res, 200, { ok: true, service: 'ai-wp-plugin-builder', engines: ['claude', 'local'], protocols: PROTOCOLS });
    }

    // Auth for everything else.
    if (req.headers['x-api-key'] !== apiKey) {
      return json(res, 401, { error: 'Invalid or missing X-API-Key.' });
    }

    if (path === '/api/platforms/test' && req.method === 'POST') {
      const body = await readBody(req).catch(() => null);
      const p = parsePlatform(body?.platform);
      if (!p) return json(res, 400, { error: `Body must include a valid "platform" (protocol: ${PROTOCOLS.join(', ')}).` });
      return json(res, 200, await testPlatform(p));
    }

    if (path === '/api/chat' && req.method === 'POST') {
      const body = await readBody(req).catch(() => null);
      const raw = Array.isArray(body?.messages) ? (body!.messages as unknown[]) : null;
      if (!raw) return json(res, 400, { error: 'Body must include a "messages" array.' });
      const messages: ChatMessage[] = raw
        .slice(-40)
        .map((m) => {
          const mm = m as { role?: string; content?: unknown };
          return {
            role: mm.role === 'assistant' ? 'assistant' : 'user',
            content: String(mm.content ?? '').slice(0, 8000),
          } as ChatMessage;
        })
        .filter((m) => m.content.trim() !== '');
      let platform: Platform | null = null;
      if (body?.platform !== undefined) {
        platform = parsePlatform(body.platform);
        if (!platform) return json(res, 400, { error: 'Invalid "platform".' });
      } else {
        const cfg = ollamaConfig();
        platform = { protocol: 'ollama', baseUrl: cfg.host, apiKey: '', model: process.env.OLLAMA_CHAT_MODEL || cfg.model };
      }
      // `distill` mode asks the model to output a single consolidated spec for building.
      const distill = body?.mode === 'distill';
      try {
        const sys = distill ? CHAT_SYSTEM + '\n\n' + DISTILL_INSTRUCTION : CHAT_SYSTEM;
        const r = await llmChat(platform, [{ role: 'system', content: sys }, ...messages], {
          temperature: distill ? 0.2 : 0.5,
          numCtx: 16384,
          timeoutMs: 5 * 60_000,
          maxTokens: 2000,
        });
        return json(res, 200, { reply: stripThink(r.content), model: platform.model });
      } catch (e) {
        return json(res, 502, { error: `Chat model error (${platform.protocol} ${platform.model}): ${e instanceof Error ? e.message : String(e)}` });
      }
    }

    if (path === '/api/build' && req.method === 'POST') {
      const body = await readBody(req).catch(() => null);
      const spec = typeof body?.spec === 'string' ? body.spec.trim() : '';
      if (!spec) return json(res, 400, { error: 'Body must include a non-empty "spec" string.' });
      if (spec.length > 20000) return json(res, 400, { error: 'Spec too large (max 20k chars).' });
      const es = engineFromBody(body);
      if ('error' in es) return json(res, 400, { error: es.error });
      const job: Job = { id: randomUUID(), engine: es.engine, status: 'queued', createdAt: Date.now(), log: [] };
      enqueue(job, spec, es);
      return json(res, 202, { jobId: job.id, status: job.status });
    }

    if (path === '/api/ingest' && req.method === 'POST') {
      const body = await readBody(req).catch(() => null);
      const zipB64 = typeof body?.zipB64 === 'string' ? body.zipB64 : '';
      const spec = typeof body?.spec === 'string' ? body.spec.trim() : '';
      if (!zipB64) return json(res, 400, { error: 'Body must include the plugin as base64 "zipB64".' });
      if (!spec) return json(res, 400, { error: 'Body must include a "spec" (the change request).' });
      if (zipB64.length > 34_000_000) return json(res, 400, { error: 'Plugin too large (max ~25 MB).' });
      const es = engineFromBody(body);
      if ('error' in es) return json(res, 400, { error: es.error });
      const job: Job = { id: randomUUID(), engine: es.engine, status: 'queued', createdAt: Date.now(), log: [] };
      enqueueIngest(job, zipB64, spec, es);
      return json(res, 202, { jobId: job.id, status: job.status });
    }

    const jobMatch = path.match(/^\/api\/jobs\/([0-9a-f-]{36})(\/zip)?$/i);
    if (jobMatch && req.method === 'GET') {
      const job = jobs.get(jobMatch[1]);
      if (!job) return json(res, 404, { error: 'Job not found.' });
      if (jobMatch[2] === '/zip') {
        const zip = job.artifact?.zip;
        if (!zip || !(await exists(zip))) return json(res, 409, { error: 'No .zip available for this job.' });
        res.setHeader('Content-Type', 'application/zip');
        res.setHeader('Content-Disposition', `attachment; filename="${basename(zip)}"`);
        createReadStream(zip).pipe(res);
        return;
      }
      return json(res, 200, {
        id: job.id,
        status: job.status,
        engine: job.engine,
        createdAt: job.createdAt,
        log: job.log.slice(-MAX_LOG_LINES),
        artifact: job.artifact,
        error: job.error,
      });
    }

    return json(res, 404, { error: 'Not found.' });
  });

  return new Promise((resolve) => {
    server.listen(port, () => {
      console.log(`\n▶ AI WP Plugin Builder service listening on http://0.0.0.0:${port}`);
      if (generatedKey) console.log(`  API key (generated): ${apiKey}\n  Set AIWPB_API_KEY to pin it.`);
      console.log('  Endpoints: GET /api/health · POST /api/chat · POST /api/build · POST /api/ingest · POST /api/platforms/test · GET /api/jobs/:id[/zip]');
      const allow = allowedHosts();
      if (allow) console.log(`  AI endpoint allow-list: ${[...allow].join(', ')}`);
      else console.warn('  WARNING: AIWPB_ALLOWED_HOSTS is not set — any AI endpoint URL sent with the API key will be called. Set it to a comma-separated list of hostnames (e.g. thing2,api.anthropic.com).');
    });
    server.on('error', (e) => {
      console.error('Server error:', e);
      resolve(1);
    });
  });
}

/** Spawn a CLI child for a job, stream its output into the job log, and read its result.json. */
function runChild(env: ServeEnv, job: Job, argv: string[], childEnv: NodeJS.ProcessEnv, resultFile: string, what: string): Promise<void> {
  const pushLog = (chunk: Buffer) => {
    for (const line of chunk.toString().split('\n')) {
      if (line.trim()) job.log.push(line.replace(/\x1b\[[0-9;]*m/g, ''));
    }
    if (job.log.length > MAX_LOG_LINES * 2) job.log = job.log.slice(-MAX_LOG_LINES);
  };

  return new Promise<void>((resolve) => {
    const child = spawn('npx', argv, { cwd: env.repoRoot, env: childEnv });
    child.stdout.on('data', pushLog);
    child.stderr.on('data', pushLog);
    child.on('close', async (code) => {
      try {
        const result = JSON.parse(await readFile(resultFile, 'utf8')) as BuildResult;
        job.artifact = result;
        job.status = result.ok ? 'done' : 'error';
        if (!result.ok) job.error = result.error || `${what} did not pass all gates.`;
      } catch {
        job.status = 'error';
        job.error = `${what} process exited with code ${code} and produced no result.`;
      }
      resolve();
    });
    child.on('error', (e) => {
      job.status = 'error';
      job.error = `Failed to start ${what.toLowerCase()} process: ${String(e)}`;
      resolve();
    });
  });
}

async function runJob(env: ServeEnv, jobsRoot: string, job: Job, specText: string, es: EngineSpec): Promise<void> {
  job.status = 'running';
  const dir = join(jobsRoot, job.id);
  await mkdir(dir, { recursive: true });
  const specFile = join(dir, 'spec.md');
  const resultFile = join(dir, 'result.json');
  await writeFile(specFile, specText, 'utf8');

  const cmd = es.engine === 'local' ? 'build-local' : 'build';
  const childEnv: NodeJS.ProcessEnv = { ...process.env, ...es.env, AIWPB_RESULT_FILE: resultFile };
  await runChild(env, job, ['tsx', 'src/run.ts', cmd, specFile], childEnv, resultFile, 'Build');
}

async function runIngestJob(
  env: ServeEnv,
  jobsRoot: string,
  job: Job,
  zipB64: string,
  changeRequest: string,
  es: EngineSpec,
): Promise<void> {
  job.status = 'running';
  const dir = join(jobsRoot, job.id);
  await mkdir(dir, { recursive: true });
  const zipFile = join(dir, 'plugin.zip');
  const changeFile = join(dir, 'change.txt');
  const resultFile = join(dir, 'result.json');
  try {
    await writeFile(zipFile, Buffer.from(zipB64, 'base64'));
  } catch {
    job.status = 'error';
    job.error = 'Could not decode the uploaded plugin.';
    return;
  }
  await writeFile(changeFile, changeRequest, 'utf8');

  const childEnv: NodeJS.ProcessEnv = { ...process.env, ...es.env, AIWPB_RESULT_FILE: resultFile };
  const argv = ['tsx', 'src/run.ts', 'ingest', zipFile, changeFile, '--engine', es.engine];
  await runChild(env, job, argv, childEnv, resultFile, 'Update');
}

// --- tiny http helpers ---
function end(res: http.ServerResponse, code: number, body: string): void {
  res.statusCode = code;
  res.end(body);
}
function json(res: http.ServerResponse, code: number, obj: unknown): void {
  res.statusCode = code;
  res.setHeader('Content-Type', 'application/json');
  res.end(JSON.stringify(obj));
}
async function readBody(req: http.IncomingMessage): Promise<Record<string, unknown>> {
  const chunks: Buffer[] = [];
  for await (const c of req) chunks.push(c as Buffer);
  const raw = Buffer.concat(chunks).toString('utf8');
  return raw ? (JSON.parse(raw) as Record<string, unknown>) : {};
}
async function exists(p: string): Promise<boolean> {
  try {
    await access(p);
    return true;
  } catch {
    return false;
  }
}
