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
 * My Plugins registry (see registry.ts):
 *   GET    /api/plugins                         -> { plugins: summary[] }
 *   POST   /api/plugins     {zipB64, ...fields}  -> import a plugin .zip -> { plugin }
 *   GET    /api/plugins/:slug                   -> { plugin } (full record + version history)
 *   PATCH  /api/plugins/:slug {name, client, status, liveUrl, notes, doNotPublish} -> { plugin }
 *   DELETE /api/plugins/:slug                   -> { deleted: true }
 *   GET    /api/plugins/:slug/versions/:id/zip|report
 *   POST   /api/plugins/:slug/restore {versionId} -> { plugin }
 *   POST   /api/plugins/:slug/edit {kind: fix|change|refactor|update, request, platform} -> { jobId }
 *   POST   /api/plugins/:slug/publish {target: 'github', token, owner?, private?, versionId?} -> { jobId }
 *   POST   /api/github/test {token}              -> { ok, login, scopes, message }
 *   POST   /api/plugins/:slug/wporg/prepare {username?, versionId?} -> { jobId }   (submission package)
 *   POST   /api/plugins/:slug/wporg/status {status: submitted|prepared, wporgSlug?} -> { plugin }
 *   POST   /api/plugins/:slug/wporg/refresh     -> { plugin, approved }  (is the slug live in the directory?)
 *   POST   /api/plugins/:slug/wporg/push {versionId?} -> { jobId }       (SVN; approved plugins only)
 *   GET    /api/wporg/config                    -> { svnInstalled, credentialsSet }
 * Successful builds and edits started through this service are recorded in the registry automatically.
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
import { mkdir, writeFile, readFile, access, rm } from 'node:fs/promises';
import { createReadStream } from 'node:fs';
import { join, basename } from 'node:path';
import type { BuildResult } from './resultFile.js';
import { ollamaConfig } from './engines/ollama.js';
import { PROTOCOLS, allowedHosts, hostAllowed, llmChat, parsePlatform, platformEnv, testPlatform, type ChatMessage, type Platform } from './engines/llm.js';
import { Registry, validSlug, EDIT_KINDS, type EditKind, type VersionKind, type PluginRecord } from './registry.js';
import { findPluginRoot } from './ingest.js';
import { exec } from './util/exec.js';

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

const EDIT_CHAT_SYSTEM = `You are a friendly WordPress developer helping a user describe an edit to an EXISTING plugin
(details of the plugin and the kind of edit follow). Talk like a person in a chat: reply in 1-3 short sentences
and ask at most one or two questions at a time. Do NOT use headings, bold labels, or bulleted forms, and do NOT
write code. For a fix, find out what goes wrong and what should happen instead. For a change, find out exactly
what should be added or behave differently. For a refactor, find out what to clean up; the behaviour must stay
the same. Only ask about what is actually unclear. Assume standard WordPress security is always applied. Once it
is clear, give a one or two sentence summary of the edit and tell the user they can click the Start button.`;

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
  /** When set, the outcome is recorded in the My Plugins registry. */
  record?: { kind: VersionKind; description: string; ai: string; slugHint?: string };
  /** Set on publish jobs: where the result is recorded. */
  publish?: { slug: string };
  /** Set on WordPress.org jobs: what to record when it finishes. */
  wporg?: { slug: string; kind: 'prepare' | 'push'; versionId: number };
}

/** How a job hands its engine to the child process: which engine runs, plus the env that configures it. */
interface EngineSpec {
  engine: 'claude' | 'local';
  env: NodeJS.ProcessEnv;
  /** Human label for the registry, e.g. "openai ./gpt-oss-120b-MXFP4.gguf". */
  ai: string;
}

const MAX_LOG_LINES = 800;

/** Resolve a request body to an engine: a `platform` object wins; otherwise the legacy engine/model fields. */
function engineFromBody(body: Record<string, unknown> | null): EngineSpec | { error: string } {
  if (body?.platform !== undefined) {
    const p = parsePlatform(body.platform);
    if (!p) return { error: platformError(body.platform) };
    return { engine: p.protocol === 'anthropic' ? 'claude' : 'local', env: platformEnv(p), ai: `${p.protocol} ${p.model || '(default model)'}` };
  }
  const engine: 'claude' | 'local' = body?.engine === 'local' ? 'local' : 'claude';
  const model = typeof body?.model === 'string' ? body.model : '';
  return { engine, env: engine === 'local' && model ? { OLLAMA_MODEL: model } : {}, ai: `${engine}${model ? ' ' + model : ''}` };
}

/** Explain why a platform was rejected — a host missing from the allow-list gets its own message. */
function platformError(raw: unknown): string {
  const r = (raw && typeof raw === 'object' ? raw : {}) as Record<string, unknown>;
  const baseUrl = typeof r.baseUrl === 'string' ? r.baseUrl.trim() : '';
  if (baseUrl && /^https?:\/\//i.test(baseUrl) && !hostAllowed(baseUrl)) {
    let host = baseUrl;
    try {
      host = new URL(baseUrl).hostname;
    } catch {
      /* keep the raw value */
    }
    return `AI endpoint host "${host}" is not on the builder service's allow-list (AIWPB_ALLOWED_HOSTS). Add it there and restart the service.`;
  }
  return `Invalid "platform" (protocol must be one of ${PROTOCOLS.join(', ')}; baseUrl must be http(s)).`;
}

/** The registry is created by runServer; runChild records finished jobs into it. */
let registry: Registry | null = null;
let serveEnv: ServeEnv | null = null;

/**
 * Keep the full source (tests + SPEC.json) of a just-recorded version. Jobs are serialized, so build/<slug>
 * still holds exactly the source that was verified and packaged.
 */
async function snapshotSource(slug: string, versionId: number, jobDir: string): Promise<void> {
  if (!registry || !serveEnv) return;
  const src = join(serveEnv.repoRoot, 'build', slug);
  if (!(await exists(src))) return;
  const out = join(jobDir, 'source.zip');
  const r = await exec('php', [join(serveEnv.harnessDir, 'bin', 'zipdir.php'), src, slug, out], { timeoutMs: 60_000 });
  if (r.code === 0) await registry.setVersionSource(slug, versionId, out);
}

/** Record a finished job in the My Plugins registry (best effort — never fails the job). */
async function recordJob(job: Job): Promise<void> {
  if (!registry) return;
  const a = job.artifact;
  if (job.wporg) {
    const w = job.wporg;
    try {
      if (w.kind === 'prepare' && a?.ok && a.zip && a.version) {
        const added = await registry.addVersion({
          slug: w.slug,
          name: a.pluginName || w.slug,
          version: a.version,
          kind: 'wporg',
          description: `Prepared for WordPress.org from version #${w.versionId}: readme brought to directory standard (Contributors ${a.wporg?.username ?? ''}, Tested up to ${a.wporg?.testedUpTo ?? ''}), screenshots and artwork generated.`,
          ai: '',
          provides: a.provides,
          zipPath: a.zip,
          reportPath: a.report ?? null,
        });
        if (serveEnv) await snapshotSource(w.slug, added.id, join(serveEnv.repoRoot, '.builder-jobs', job.id));
        if (a.wporg?.assetsZip) await registry.setVersionAssets(w.slug, added.id, a.wporg.assetsZip);
        const cur = (await registry.get(w.slug))?.published?.wporg;
        await registry.setWporg(
          w.slug,
          { status: cur?.status === 'approved' || cur?.status === 'submitted' ? cur.status : 'prepared', slug: a.wporg?.wporgSlug || w.slug, preparedVersionId: added.id },
          `Prepared v${a.version} for WordPress.org (version #${added.id}).`,
        );
        job.log.push(`Recorded in My Plugins: v${a.version} (WordPress.org-ready) with its assets.`);
      } else if (w.kind === 'push' && a?.ok && a.wporgPush) {
        const cur = (await registry.get(w.slug))?.published?.wporg;
        const pushes = [...(cur?.pushes ?? []), { versionId: a.wporgPush.versionId, version: a.wporgPush.version, revision: a.wporgPush.revision, at: new Date().toISOString() }];
        await registry.setWporg(w.slug, { pushes }, `Pushed v${a.wporgPush.version} to WordPress.org (SVN r${a.wporgPush.revision}).`);
      } else {
        await registry.addActivity(w.slug, false, `WordPress.org ${w.kind} failed: ${job.error || a?.error || 'unknown error'}`);
      }
    } catch (e) {
      job.log.push(`Could not record the WordPress.org result: ${e instanceof Error ? e.message : String(e)}`);
    }
    return;
  }
  if (job.publish) {
    try {
      if (a?.ok && a.github) {
        const g = a.github;
        await registry.recordGithub(
          job.publish.slug,
          { owner: g.owner, repo: g.repo, url: g.url, private: g.private },
          { versionId: g.versionId, version: a.version ?? '', tag: g.tag, commit: g.commit, releaseUrl: g.releaseUrl, at: new Date().toISOString() },
        );
      } else {
        await registry.addActivity(job.publish.slug, false, `GitHub publish failed: ${job.error || a?.error || 'unknown error'}`);
      }
    } catch (e) {
      job.log.push(`Could not record the publish in My Plugins: ${e instanceof Error ? e.message : String(e)}`);
    }
    return;
  }
  if (!job.record) return;
  try {
    if (a?.ok && a.zip && a.slug && a.version) {
      const added = await registry.addVersion({
        slug: a.slug,
        name: a.pluginName || a.slug,
        version: a.version,
        kind: job.record.kind,
        description: job.record.description,
        ai: job.record.ai,
        costUsd: a.costUsd,
        iterations: a.iterations,
        auditFindings: a.auditFindings ?? null,
        provides: a.provides,
        zipPath: a.zip,
        reportPath: a.report ?? null,
      });
      if (serveEnv) await snapshotSource(a.slug, added.id, join(serveEnv.repoRoot, '.builder-jobs', job.id));
      job.log.push(`Recorded in My Plugins: ${a.slug} v${a.version}.`);
    } else {
      const slug = a?.slug || job.record.slugHint;
      if (slug) await registry.addActivity(slug, false, `${job.record.kind} failed: ${job.error || a?.error || 'unknown error'}`);
    }
  } catch (e) {
    job.log.push(`Could not record in My Plugins: ${e instanceof Error ? e.message : String(e)}`);
  }
}

/** List view of a record (no version history). */
function summary(rec: PluginRecord): Record<string, unknown> {
  const cur = rec.versions.find((v) => v.id === rec.currentVersionId) ?? rec.versions[rec.versions.length - 1];
  return {
    slug: rec.slug,
    name: rec.name,
    client: rec.client,
    status: rec.status,
    liveUrl: rec.liveUrl,
    notes: rec.notes,
    doNotPublish: rec.doNotPublish,
    createdAt: rec.createdAt,
    updatedAt: rec.updatedAt,
    versionsCount: rec.versions.length,
    wporg: rec.published?.wporg ? { status: rec.published.wporg.status, slug: rec.published.wporg.slug, url: rec.published.wporg.url ?? '' } : null,
    github: rec.published?.github ? { url: rec.published.github.url, private: rec.published.github.private, lastTag: rec.published.github.releases.at(-1)?.tag ?? '' } : null,
    totalCostUsd: rec.versions.reduce((t, v) => t + (v.costUsd || 0), 0),
    current: cur
      ? { id: cur.id, version: cur.version, kind: cur.kind, ai: cur.ai, createdAt: cur.createdAt, auditFindings: cur.auditFindings, provides: cur.provides }
      : null,
  };
}

export async function runServer(_args: string[], env: ServeEnv): Promise<number> {
  const port = Number(process.env.AIWPB_PORT || 8787);
  const apiKey = process.env.AIWPB_API_KEY || randomBytes(24).toString('hex');
  const generatedKey = !process.env.AIWPB_API_KEY;
  const jobsRoot = join(env.repoRoot, '.builder-jobs');
  await mkdir(jobsRoot, { recursive: true });

  registry = new Registry(env.repoRoot);
  serveEnv = env;
  const reg = registry;
  const jobs = new Map<string, Job>();
  let chain: Promise<void> = Promise.resolve(); // serialize builds

  function enqueue(job: Job, specText: string, es: EngineSpec): void {
    jobs.set(job.id, job);
    chain = chain.then(() => runJob(env, jobsRoot, job, specText, es));
  }

  function enqueueIngest(job: Job, source: IngestSource, changeRequest: string, es: EngineSpec, kind?: EditKind): void {
    jobs.set(job.id, job);
    chain = chain.then(() => runIngestJob(env, jobsRoot, job, source, changeRequest, es, kind));
  }

  const server = http.createServer(async (req, res) => {
    const url = new URL(req.url || '/', `http://localhost`);
    const path = url.pathname;
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, X-API-Key');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS');
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
      if (!p) return json(res, 400, { error: body?.platform === undefined ? 'Body must include a "platform".' : platformError(body.platform) });
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
      const context = typeof body?.context === 'string' ? body.context.slice(0, 4000) : '';
      try {
        const sys =
          body?.mode === 'edit'
            ? EDIT_CHAT_SYSTEM + (context ? '\n\nTHE PLUGIN AND THE EDIT:\n' + context : '')
            : distill
              ? CHAT_SYSTEM + '\n\n' + DISTILL_INSTRUCTION
              : CHAT_SYSTEM;
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
      const job: Job = {
        id: randomUUID(),
        engine: es.engine,
        status: 'queued',
        createdAt: Date.now(),
        log: [],
        record: { kind: 'build', description: spec, ai: es.ai },
      };
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
      const job: Job = {
        id: randomUUID(),
        engine: es.engine,
        status: 'queued',
        createdAt: Date.now(),
        log: [],
        record: { kind: 'update', description: spec, ai: es.ai },
      };
      enqueueIngest(job, { zipB64 }, spec, es);
      return json(res, 202, { jobId: job.id, status: job.status });
    }

    if (path === '/api/wporg/config' && req.method === 'GET') {
      const { svnBinary } = await import('./publishWporg.js');
      return json(res, 200, {
        svnInstalled: !!(await svnBinary()),
        credentialsSet: !!(process.env.AIWPB_WPORG_SVN_USER && process.env.AIWPB_WPORG_SVN_PASSWORD),
        svnUser: process.env.AIWPB_WPORG_SVN_USER || '',
      });
    }

    if (path === '/api/github/test' && req.method === 'POST') {
      const body = await readBody(req).catch(() => null);
      const token = typeof body?.token === 'string' ? body.token.trim() : '';
      if (!token) return json(res, 400, { error: 'Body must include a GitHub "token".' });
      try {
        const r = await fetch('https://api.github.com/user', {
          headers: { authorization: `Bearer ${token}`, accept: 'application/vnd.github+json', 'user-agent': 'ai-wp-plugin-builder' },
          redirect: 'error',
          signal: AbortSignal.timeout(15_000),
        });
        const data = (await r.json().catch(() => ({}))) as { login?: string; message?: string };
        if (r.status !== 200) return json(res, 200, { ok: false, message: `GitHub rejected the token (HTTP ${r.status}): ${data.message ?? ''}` });
        const scopes = r.headers.get('x-oauth-scopes');
        const fineGrained = scopes === null;
        const canRepo = fineGrained || /\brepo\b/.test(scopes ?? '');
        return json(res, 200, {
          ok: canRepo,
          login: data.login,
          scopes: scopes ?? '(fine-grained token)',
          message: canRepo
            ? `Token works: signed in as ${data.login}${fineGrained ? ' (fine-grained token — it needs Administration + Contents read/write on all repositories to create repos)' : ' with the "repo" scope'}.`
            : `Signed in as ${data.login}, but the token lacks the "repo" scope, so it cannot create private repos or push.`,
        });
      } catch (e) {
        return json(res, 200, { ok: false, message: `Could not reach GitHub: ${e instanceof Error ? e.message : String(e)}` });
      }
    }

    // ---------- My Plugins registry ----------
    if (path === '/api/plugins' && req.method === 'GET') {
      return json(res, 200, { plugins: (await reg.list()).map(summary) });
    }

    if (path === '/api/plugins' && req.method === 'POST') {
      const body = await readBody(req).catch(() => null);
      const zipB64 = typeof body?.zipB64 === 'string' ? body.zipB64 : '';
      if (!zipB64) return json(res, 400, { error: 'Body must include the plugin as base64 "zipB64".' });
      if (zipB64.length > 34_000_000) return json(res, 400, { error: 'Plugin too large (max ~25 MB).' });
      try {
        const imp = await importZip(env, jobsRoot, zipB64);
        await reg.addVersion({
          slug: imp.slug,
          name: imp.name,
          version: imp.version,
          kind: 'import',
          description: typeof body?.description === 'string' && body.description.trim() ? body.description.trim() : 'Imported from a .zip.',
          ai: '',
          zipPath: imp.zipPath,
        });
        await rm(imp.workDir, { recursive: true, force: true });
        const rec = await reg.update(imp.slug, body ?? {});
        return json(res, 201, { plugin: rec });
      } catch (e) {
        return json(res, 400, { error: e instanceof Error ? e.message : String(e) });
      }
    }

    const pluginMatch = path.match(/^\/api\/plugins\/([a-z0-9][a-z0-9-]{1,80})(?:\/(restore|edit|publish|wporg\/prepare|wporg\/status|wporg\/refresh|wporg\/push)|\/versions\/(\d+)\/(zip|report|assets))?$/);
    if (pluginMatch) {
      const [, slug, action, versionIdRaw, versionFile] = pluginMatch;
      if (!validSlug(slug)) return json(res, 400, { error: 'Invalid plugin slug.' });

      if (!action && !versionIdRaw) {
        if (req.method === 'GET') {
          const rec = await reg.get(slug);
          return rec ? json(res, 200, { plugin: rec }) : json(res, 404, { error: 'Plugin not found.' });
        }
        if (req.method === 'PATCH') {
          const body = await readBody(req).catch(() => null);
          const rec = await reg.update(slug, body ?? {});
          return rec ? json(res, 200, { plugin: rec }) : json(res, 404, { error: 'Plugin not found.' });
        }
        if (req.method === 'DELETE') {
          return (await reg.remove(slug)) ? json(res, 200, { deleted: true }) : json(res, 404, { error: 'Plugin not found.' });
        }
      }

      if (versionIdRaw && req.method === 'GET') {
        const file = await reg.versionFile(slug, Number(versionIdRaw), versionFile === 'zip' ? 'zip' : versionFile === 'assets' ? 'assets' : 'report');
        if (!file || !(await exists(file))) return json(res, 404, { error: 'That version file was not found.' });
        if (versionFile === 'zip' || versionFile === 'assets') {
          res.setHeader('Content-Type', 'application/zip');
          res.setHeader('Content-Disposition', `attachment; filename="${basename(file)}"`);
        } else {
          res.setHeader('Content-Type', 'text/markdown; charset=utf-8');
        }
        createReadStream(file).pipe(res);
        return;
      }

      if (action === 'restore' && req.method === 'POST') {
        const body = await readBody(req).catch(() => null);
        const rec = await reg.restore(slug, Number(body?.versionId));
        return rec ? json(res, 200, { plugin: rec }) : json(res, 404, { error: 'Plugin or version not found.' });
      }

      if (action === 'edit' && req.method === 'POST') {
        const body = await readBody(req).catch(() => null);
        const kind = String(body?.kind ?? '');
        if (!(EDIT_KINDS as readonly string[]).includes(kind)) return json(res, 400, { error: `"kind" must be one of: ${EDIT_KINDS.join(', ')}.` });
        const request = typeof body?.request === 'string' ? body.request.trim() : '';
        if (!request) return json(res, 400, { error: 'Describe the change in "request".' });
        if (request.length > 20000) return json(res, 400, { error: 'Request too large (max 20k chars).' });
        const rec = await reg.get(slug);
        const cur = rec ? reg.current(rec) : null;
        if (!rec || !cur) return json(res, 404, { error: 'Plugin not found.' });
        const zipPath = reg.abs(cur.zipFile);
        if (!(await exists(zipPath))) return json(res, 409, { error: 'The current version\'s .zip is missing on the service.' });
        const es = engineFromBody(body);
        if ('error' in es) return json(res, 400, { error: es.error });
        const job: Job = {
          id: randomUUID(),
          engine: es.engine,
          status: 'queued',
          createdAt: Date.now(),
          log: [],
          record: { kind: kind as EditKind, description: request, ai: es.ai, slugHint: slug },
        };
        enqueueIngest(job, { zipPath }, request, es, kind as EditKind);
        return json(res, 202, { jobId: job.id, status: job.status });
      }

      if (action?.startsWith('wporg/') && req.method === 'POST') {
        const body = await readBody(req).catch(() => null);
        const rec = await reg.get(slug);
        if (!rec) return json(res, 404, { error: 'Plugin not found.' });
        const sub = action.slice('wporg/'.length);
        const wp = rec.published?.wporg;

        if (sub === 'status') {
          const status = body?.status === 'submitted' ? 'submitted' : body?.status === 'prepared' ? 'prepared' : '';
          if (!status) return json(res, 400, { error: '"status" must be "submitted" or "prepared".' });
          if (!wp) return json(res, 409, { error: 'Prepare it for WordPress.org first.' });
          const wporgSlug = typeof body?.wporgSlug === 'string' && /^[a-z0-9][a-z0-9-]{1,80}$/.test(body.wporgSlug) ? body.wporgSlug : wp.slug;
          const updated = await reg.setWporg(
            slug,
            { status, slug: wporgSlug, ...(status === 'submitted' ? { submittedAt: new Date().toISOString() } : {}) },
            status === 'submitted' ? 'Marked as submitted to WordPress.org (awaiting review).' : 'WordPress.org status reset to "prepared".',
          );
          return json(res, 200, { plugin: updated });
        }

        if (sub === 'refresh') {
          if (!wp) return json(res, 409, { error: 'Prepare it for WordPress.org first.' });
          const { slugStatus } = await import('./wporg.js');
          const st = await slugStatus(wp.slug);
          if (st.state === 'taken' && wp.status !== 'approved') {
            const updated = await reg.setWporg(
              slug,
              { status: 'approved', approvedAt: new Date().toISOString(), url: `https://wordpress.org/plugins/${wp.slug}/` },
              `Approved: live in the WordPress.org directory as "${st.name}".`,
            );
            return json(res, 200, { plugin: updated, approved: true, message: `Live in the directory as "${st.name}".` });
          }
          const message =
            wp.status === 'approved'
              ? 'Already approved.'
              : st.state === 'unknown'
                ? 'Could not reach WordPress.org to check.'
                : `"${wp.slug}" is not in the directory yet. If the reviewers assigned a different slug, enter it.`;
          return json(res, 200, { plugin: rec, approved: wp.status === 'approved', message });
        }

        if (rec.doNotPublish) return json(res, 409, { error: `"${rec.name}" is marked do-not-publish.` });
        const versionId = body?.versionId !== undefined ? Number(body.versionId) : rec.currentVersionId;
        if (!rec.versions.some((v) => v.id === versionId)) return json(res, 404, { error: 'That version was not found.' });

        let argv: string[];
        let childEnv: NodeJS.ProcessEnv = { ...process.env };
        if (sub === 'prepare') {
          const username = typeof body?.username === 'string' ? body.username.trim() : '';
          if (username && !/^[A-Za-z0-9-]{1,50}$/.test(username)) return json(res, 400, { error: 'Invalid WordPress.org username.' });
          childEnv = { ...childEnv, AIWPB_WPORG_USER: username || 'snavazio' };
          argv = ['tsx', 'src/run.ts', 'wporg-prepare', slug, String(versionId)];
        } else if (sub === 'push') {
          if (wp?.status !== 'approved') return json(res, 409, { error: 'Only approved plugins can be pushed. Use "Check approval" once WordPress.org has approved it.' });
          argv = ['tsx', 'src/run.ts', 'wporg-push', slug, String(versionId), wp.slug];
        } else {
          return json(res, 404, { error: 'Unknown WordPress.org action.' });
        }
        const job: Job = {
          id: randomUUID(),
          engine: 'local',
          status: 'queued',
          createdAt: Date.now(),
          log: [],
          wporg: { slug, kind: sub === 'prepare' ? 'prepare' : 'push', versionId },
        };
        jobs.set(job.id, job);
        chain = chain.then(async () => {
          job.status = 'running';
          const dir = join(jobsRoot, job.id);
          await mkdir(dir, { recursive: true });
          const resultFile = join(dir, 'result.json');
          await runChild(env, job, argv, { ...childEnv, AIWPB_RESULT_FILE: resultFile }, resultFile, sub === 'prepare' ? 'WordPress.org preparation' : 'WordPress.org push');
        });
        return json(res, 202, { jobId: job.id, status: job.status });
      }

      if (action === 'publish' && req.method === 'POST') {
        const body = await readBody(req).catch(() => null);
        if (body?.target !== 'github') return json(res, 400, { error: 'Only target "github" is supported so far.' });
        const token = typeof body?.token === 'string' ? body.token.trim() : '';
        if (!token) return json(res, 400, { error: 'No GitHub token. Save one under AI Plugin Builder → Settings → GitHub.' });
        const owner = typeof body?.owner === 'string' ? body.owner.trim() : '';
        if (owner && !/^[A-Za-z0-9](?:[A-Za-z0-9-]{0,38})$/.test(owner)) return json(res, 400, { error: 'Invalid GitHub account name.' });
        const rec = await reg.get(slug);
        if (!rec) return json(res, 404, { error: 'Plugin not found.' });
        if (rec.doNotPublish) return json(res, 409, { error: `"${rec.name}" is marked do-not-publish.` });
        const versionId = body?.versionId !== undefined ? Number(body.versionId) : rec.currentVersionId;
        if (!rec.versions.some((v) => v.id === versionId)) return json(res, 404, { error: 'That version was not found.' });
        const job: Job = { id: randomUUID(), engine: 'local', status: 'queued', createdAt: Date.now(), log: [], publish: { slug } };
        jobs.set(job.id, job);
        const childEnv: NodeJS.ProcessEnv = {
          ...process.env,
          GITHUB_TOKEN: token,
          AIWPB_GH_OWNER: owner,
          AIWPB_GH_PRIVATE: body?.private === false ? '0' : '1',
        };
        chain = chain.then(async () => {
          job.status = 'running';
          const dir = join(jobsRoot, job.id);
          await mkdir(dir, { recursive: true });
          const resultFile = join(dir, 'result.json');
          await runChild(env, job, ['tsx', 'src/run.ts', 'publish-github', slug, String(versionId)], { ...childEnv, AIWPB_RESULT_FILE: resultFile }, resultFile, 'Publish');
        });
        return json(res, 202, { jobId: job.id, status: job.status });
      }

      return json(res, 405, { error: 'Method not allowed.' });
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
      console.log('  Endpoints: GET /api/health · POST /api/chat · POST /api/build · POST /api/ingest · POST /api/platforms/test · GET /api/jobs/:id[/zip] · /api/plugins[/:slug[/edit|/restore|/versions/:id/zip|report]]');
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
      await recordJob(job);
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

/** Where an edit job's starting .zip comes from: an upload (base64) or a stored registry version. */
type IngestSource = { zipB64: string } | { zipPath: string };

async function runIngestJob(
  env: ServeEnv,
  jobsRoot: string,
  job: Job,
  source: IngestSource,
  changeRequest: string,
  es: EngineSpec,
  kind?: EditKind,
): Promise<void> {
  job.status = 'running';
  const dir = join(jobsRoot, job.id);
  await mkdir(dir, { recursive: true });
  const changeFile = join(dir, 'change.txt');
  const resultFile = join(dir, 'result.json');
  let zipFile: string;
  if ('zipPath' in source) {
    zipFile = source.zipPath;
  } else {
    zipFile = join(dir, 'plugin.zip');
    try {
      await writeFile(zipFile, Buffer.from(source.zipB64, 'base64'));
    } catch {
      job.status = 'error';
      job.error = 'Could not decode the uploaded plugin.';
      return;
    }
  }
  await writeFile(changeFile, changeRequest, 'utf8');

  const childEnv: NodeJS.ProcessEnv = { ...process.env, ...es.env, AIWPB_RESULT_FILE: resultFile };
  const argv = ['tsx', 'src/run.ts', 'ingest', zipFile, changeFile, '--engine', es.engine, ...(kind ? ['--kind', kind] : [])];
  await runChild(env, job, argv, childEnv, resultFile, kind ? kind[0].toUpperCase() + kind.slice(1) : 'Update');
}

/**
 * Import a plugin .zip into the registry: extract it, find the plugin, and re-package it so the stored .zip
 * always has a single root folder named after the slug (and honours the plugin's .distignore).
 */
async function importZip(
  env: ServeEnv,
  jobsRoot: string,
  zipB64: string,
): Promise<{ slug: string; name: string; version: string; zipPath: string; workDir: string }> {
  const workDir = join(jobsRoot, `import-${randomUUID()}`);
  await mkdir(workDir, { recursive: true });
  const inZip = join(workDir, 'upload.zip');
  await writeFile(inZip, Buffer.from(zipB64, 'base64'));
  const extractDir = join(workDir, 'x');
  const ex = await exec('php', [join(env.harnessDir, 'bin', 'unzip.php'), inZip, extractDir], { timeoutMs: 60_000 });
  if (ex.code !== 0) {
    await rm(workDir, { recursive: true, force: true });
    throw new Error('Could not extract that .zip.');
  }
  const root = await findPluginRoot(extractDir);
  if (!root) {
    await rm(workDir, { recursive: true, force: true });
    throw new Error('No WordPress plugin (a PHP file with a "Plugin Name:" header) was found in that .zip.');
  }
  const slug = basename(root.dir) === 'x' ? basename(root.mainFile, '.php') : basename(root.dir);
  if (!validSlug(slug)) {
    await rm(workDir, { recursive: true, force: true });
    throw new Error(`The plugin folder name "${slug}" is not a valid slug (lowercase letters, digits and dashes).`);
  }
  const outZip = join(workDir, `${slug}.${root.version}.zip`);
  const pkg = await exec('php', [join(env.harnessDir, 'bin', 'package.php'), root.dir, slug, outZip], { timeoutMs: 60_000 });
  if (pkg.code !== 0) {
    await rm(workDir, { recursive: true, force: true });
    throw new Error('Could not re-package the plugin.');
  }
  return { slug, name: root.name, version: root.version, zipPath: outZip, workDir };
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
