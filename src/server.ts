/**
 * Builder service — wraps the CLI generator as a small authenticated HTTP API so the companion
 * WordPress plugin (or any client) can submit a spec and receive a verified .zip.
 *
 *   AIWPB_API_KEY=<secret> npm run serve            # default port 8787
 *   AIWPB_PORT=9000 npm run serve
 *
 * Endpoints (all under /api, key required via X-API-Key except /api/health):
 *   GET  /api/health                 -> { ok, service, engines }
 *   POST /api/build   {spec, engine} -> { jobId }         (engine: "claude" | "local")
 *   GET  /api/jobs/:id               -> { id, status, engine, log[], artifact? }
 *   GET  /api/jobs/:id/zip           -> streams the built .zip
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
import { ollamaConfig, ollamaChat, type ChatMessage } from './engines/ollama.js';

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

const MAX_LOG_LINES = 800;

export async function runServer(_args: string[], env: ServeEnv): Promise<number> {
  const port = Number(process.env.AIWPB_PORT || 8787);
  const apiKey = process.env.AIWPB_API_KEY || randomBytes(24).toString('hex');
  const generatedKey = !process.env.AIWPB_API_KEY;
  const jobsRoot = join(env.repoRoot, '.builder-jobs');
  await mkdir(jobsRoot, { recursive: true });

  const jobs = new Map<string, Job>();
  let chain: Promise<void> = Promise.resolve(); // serialize builds

  function enqueue(job: Job, specText: string, model?: string): void {
    jobs.set(job.id, job);
    chain = chain.then(() => runJob(env, jobsRoot, job, specText, model));
  }

  function enqueueIngest(job: Job, zipB64: string, changeRequest: string, model?: string): void {
    jobs.set(job.id, job);
    chain = chain.then(() => runIngestJob(env, jobsRoot, job, zipB64, changeRequest, model));
  }

  const server = http.createServer(async (req, res) => {
    const url = new URL(req.url || '/', `http://localhost`);
    const path = url.pathname;
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, X-API-Key');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    if (req.method === 'OPTIONS') return end(res, 204, '');

    if (path === '/api/health') {
      return json(res, 200, { ok: true, service: 'ai-wp-plugin-builder', engines: ['claude', 'local'] });
    }

    // Auth for everything else.
    if (req.headers['x-api-key'] !== apiKey) {
      return json(res, 401, { error: 'Invalid or missing X-API-Key.' });
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
      // `distill` mode asks the model to output a single consolidated spec for building.
      const distill = body?.mode === 'distill';
      try {
        const cfg = ollamaConfig();
        const model = process.env.OLLAMA_CHAT_MODEL || cfg.model;
        const sys = distill ? CHAT_SYSTEM + '\n\n' + DISTILL_INSTRUCTION : CHAT_SYSTEM;
        const r = await ollamaChat(
          { ...cfg, model },
          [{ role: 'system', content: sys }, ...messages],
          { temperature: distill ? 0.2 : 0.5, numCtx: 16384, timeoutMs: 5 * 60_000 },
        );
        return json(res, 200, { reply: stripThink(r.content), model });
      } catch (e) {
        return json(res, 502, { error: `Chat model error: ${String(e)}` });
      }
    }

    if (path === '/api/build' && req.method === 'POST') {
      const body = await readBody(req).catch(() => null);
      const spec = typeof body?.spec === 'string' ? body.spec.trim() : '';
      const engine: 'claude' | 'local' = body?.engine === 'local' ? 'local' : 'claude';
      const model = typeof body?.model === 'string' ? body.model : undefined;
      if (!spec) return json(res, 400, { error: 'Body must include a non-empty "spec" string.' });
      if (spec.length > 20000) return json(res, 400, { error: 'Spec too large (max 20k chars).' });
      const job: Job = { id: randomUUID(), engine, status: 'queued', createdAt: Date.now(), log: [] };
      enqueue(job, spec, model);
      return json(res, 202, { jobId: job.id, status: job.status });
    }

    if (path === '/api/ingest' && req.method === 'POST') {
      const body = await readBody(req).catch(() => null);
      const zipB64 = typeof body?.zipB64 === 'string' ? body.zipB64 : '';
      const spec = typeof body?.spec === 'string' ? body.spec.trim() : '';
      const engine: 'claude' | 'local' = body?.engine === 'local' ? 'local' : 'claude';
      const model = typeof body?.model === 'string' ? body.model : undefined;
      if (!zipB64) return json(res, 400, { error: 'Body must include the plugin as base64 "zipB64".' });
      if (!spec) return json(res, 400, { error: 'Body must include a "spec" (the change request).' });
      if (zipB64.length > 34_000_000) return json(res, 400, { error: 'Plugin too large (max ~25 MB).' });
      const job: Job = { id: randomUUID(), engine, status: 'queued', createdAt: Date.now(), log: [] };
      enqueueIngest(job, zipB64, spec, model);
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
      console.log('  Endpoints: GET /api/health · POST /api/build · GET /api/jobs/:id[/zip]');
    });
    server.on('error', (e) => {
      console.error('Server error:', e);
      resolve(1);
    });
  });
}

async function runJob(env: ServeEnv, jobsRoot: string, job: Job, specText: string, model?: string): Promise<void> {
  job.status = 'running';
  const dir = join(jobsRoot, job.id);
  await mkdir(dir, { recursive: true });
  const specFile = join(dir, 'spec.md');
  const resultFile = join(dir, 'result.json');
  await writeFile(specFile, specText, 'utf8');

  const cmd = job.engine === 'local' ? 'build-local' : 'build';
  const childEnv: NodeJS.ProcessEnv = { ...process.env, AIWPB_RESULT_FILE: resultFile };
  if (model) childEnv.OLLAMA_MODEL = model;

  const pushLog = (chunk: Buffer) => {
    for (const line of chunk.toString().split('\n')) {
      if (line.trim()) job.log.push(line.replace(/\x1b\[[0-9;]*m/g, ''));
    }
    if (job.log.length > MAX_LOG_LINES * 2) job.log = job.log.slice(-MAX_LOG_LINES);
  };

  await new Promise<void>((resolve) => {
    const child = spawn('npx', ['tsx', 'src/run.ts', cmd, specFile], { cwd: env.repoRoot, env: childEnv });
    child.stdout.on('data', pushLog);
    child.stderr.on('data', pushLog);
    child.on('close', async (code) => {
      try {
        const result = JSON.parse(await readFile(resultFile, 'utf8')) as BuildResult;
        job.artifact = result;
        job.status = result.ok ? 'done' : 'error';
        if (!result.ok) job.error = result.error || 'Build did not pass all gates.';
      } catch {
        job.status = 'error';
        job.error = `Build process exited with code ${code} and produced no result.`;
      }
      resolve();
    });
    child.on('error', (e) => {
      job.status = 'error';
      job.error = `Failed to start build process: ${String(e)}`;
      resolve();
    });
  });
}

async function runIngestJob(
  env: ServeEnv,
  jobsRoot: string,
  job: Job,
  zipB64: string,
  changeRequest: string,
  model?: string,
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

  const childEnv: NodeJS.ProcessEnv = { ...process.env, AIWPB_RESULT_FILE: resultFile };
  if (model) childEnv.OLLAMA_MODEL = model;
  const argv = ['tsx', 'src/run.ts', 'ingest', zipFile, changeFile, '--engine', job.engine];
  if (model) argv.push('--model', model);

  const pushLog = (chunk: Buffer) => {
    for (const line of chunk.toString().split('\n')) {
      if (line.trim()) job.log.push(line.replace(/\x1b\[[0-9;]*m/g, ''));
    }
    if (job.log.length > MAX_LOG_LINES * 2) job.log = job.log.slice(-MAX_LOG_LINES);
  };

  await new Promise<void>((resolve) => {
    const child = spawn('npx', argv, { cwd: env.repoRoot, env: childEnv });
    child.stdout.on('data', pushLog);
    child.stderr.on('data', pushLog);
    child.on('close', async (code) => {
      try {
        const result = JSON.parse(await readFile(resultFile, 'utf8')) as BuildResult;
        job.artifact = result;
        job.status = result.ok ? 'done' : 'error';
        if (!result.ok) job.error = result.error || 'Update did not pass all gates.';
      } catch {
        job.status = 'error';
        job.error = `Update process exited with code ${code} and produced no result.`;
      }
      resolve();
    });
    child.on('error', (e) => {
      job.status = 'error';
      job.error = `Failed to start update process: ${String(e)}`;
      resolve();
    });
  });
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
