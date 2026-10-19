/**
 * Plugin registry: the "My Plugins" list behind the WordPress admin screen.
 *
 * One record per plugin (name, client, status, live URL, notes, do-not-publish flag) with a version history.
 * Every version keeps its own .zip and report, so any version can be downloaded, restored, or used as the
 * starting point of an edit. Builds and edits started through the builder service are recorded
 * automatically; a plugin can also be imported from a .zip.
 *
 * Stored on the service machine under .builder-data/ (git-ignored): client names and notes never go to git.
 *   .builder-data/registry.json
 *   .builder-data/plugins/<slug>/<versionId>/<slug>.<version>.zip  (+ report.md)
 */
import { readFile, writeFile, mkdir, rm, copyFile, rename } from 'node:fs/promises';
import { join } from 'node:path';

export const STATUSES = ['draft', 'in-review', 'delivered', 'live', 'archived'] as const;
export type Status = (typeof STATUSES)[number];

export const VERSION_KINDS = ['build', 'import', 'fix', 'change', 'refactor', 'update'] as const;
export type VersionKind = (typeof VERSION_KINDS)[number];

export const EDIT_KINDS = ['fix', 'change', 'refactor', 'update'] as const;
export type EditKind = (typeof EDIT_KINDS)[number];

export interface VersionRecord {
  /** Sequence number within the plugin (1, 2, 3…). Version strings can repeat (e.g. two fresh 1.0.0 builds). */
  id: number;
  version: string;
  kind: VersionKind;
  /** What was asked for: the spec or change request, trimmed. */
  description: string;
  /** e.g. "anthropic claude-sonnet-5-5" or "openai ./gpt-oss-120b-MXFP4.gguf". */
  ai: string;
  costUsd: number;
  iterations: number;
  /** Security-auditor findings left at the end (Claude engine only; null when no auditor ran). */
  auditFindings: number | null;
  provides: string;
  createdAt: string;
  /** Paths relative to .builder-data/. */
  zipFile: string;
  reportFile: string | null;
  /** Full source snapshot (tests + SPEC.json included). Absent for imports and for versions recorded before it existed. */
  sourceFile?: string | null;
}

/** One version pushed to a GitHub repo. */
export interface GithubRelease {
  versionId: number;
  version: string;
  tag: string;
  commit: string;
  releaseUrl: string;
  at: string;
}

export interface GithubPublish {
  owner: string;
  repo: string;
  url: string;
  private: boolean;
  releases: GithubRelease[];
}

export interface ActivityEntry {
  at: string;
  ok: boolean;
  message: string;
}

export interface PluginRecord {
  slug: string;
  name: string;
  client: string;
  status: Status;
  liveUrl: string;
  notes: string;
  doNotPublish: boolean;
  createdAt: string;
  updatedAt: string;
  /** The version edits start from and that "current" downloads/installs use. */
  currentVersionId: number;
  versions: VersionRecord[];
  activity: ActivityEntry[];
  /** Where the plugin has been published (step 2: GitHub; step 3 will add wordpress.org). */
  published?: { github?: GithubPublish };
}

interface RegistryFile {
  plugins: Record<string, PluginRecord>;
}

const MAX_ACTIVITY = 50;
const SLUG_RE = /^[a-z0-9][a-z0-9-]{1,80}$/;

export function validSlug(slug: string): boolean {
  return SLUG_RE.test(slug);
}

export class Registry {
  private readonly dataDir: string;
  private readonly file: string;
  /** Serializes every read-modify-write so concurrent requests cannot lose updates. */
  private lock: Promise<unknown> = Promise.resolve();

  constructor(repoRoot: string) {
    this.dataDir = join(repoRoot, '.builder-data');
    this.file = join(this.dataDir, 'registry.json');
  }

  abs(rel: string): string {
    return join(this.dataDir, rel);
  }

  private async load(): Promise<RegistryFile> {
    try {
      const data = JSON.parse(await readFile(this.file, 'utf8')) as RegistryFile;
      return data && typeof data.plugins === 'object' ? data : { plugins: {} };
    } catch {
      return { plugins: {} };
    }
  }

  private async save(data: RegistryFile): Promise<void> {
    await mkdir(this.dataDir, { recursive: true });
    // Write-then-rename so a crash mid-write cannot leave a truncated registry.
    const tmp = this.file + '.tmp';
    await writeFile(tmp, JSON.stringify(data, null, 2) + '\n', 'utf8');
    await rename(tmp, this.file);
  }

  private exclusive<T>(fn: (data: RegistryFile) => Promise<T>): Promise<T> {
    const run = this.lock.then(async () => {
      const data = await this.load();
      return fn(data);
    });
    this.lock = run.catch(() => undefined);
    return run;
  }

  async list(): Promise<PluginRecord[]> {
    const data = await this.load();
    return Object.values(data.plugins).sort((a, b) => b.updatedAt.localeCompare(a.updatedAt));
  }

  async get(slug: string): Promise<PluginRecord | null> {
    return (await this.load()).plugins[slug] ?? null;
  }

  current(rec: PluginRecord): VersionRecord | null {
    return rec.versions.find((v) => v.id === rec.currentVersionId) ?? rec.versions[rec.versions.length - 1] ?? null;
  }

  /**
   * Record a new version: copies the .zip (and report) into the registry, creating the plugin record on
   * first sight. The new version becomes current.
   */
  async addVersion(p: {
    slug: string;
    name: string;
    version: string;
    kind: VersionKind;
    description: string;
    ai: string;
    costUsd?: number;
    iterations?: number;
    auditFindings?: number | null;
    provides?: string;
    zipPath: string;
    reportPath?: string | null;
  }): Promise<VersionRecord> {
    if (!validSlug(p.slug)) throw new Error(`Invalid plugin slug "${p.slug}".`);
    return this.exclusive(async (data) => {
      const now = new Date().toISOString();
      let rec = data.plugins[p.slug];
      if (!rec) {
        rec = {
          slug: p.slug,
          name: p.name || p.slug,
          client: '',
          status: 'draft',
          liveUrl: '',
          notes: '',
          doNotPublish: false,
          createdAt: now,
          updatedAt: now,
          currentVersionId: 0,
          versions: [],
          activity: [],
        };
        data.plugins[p.slug] = rec;
      }
      const id = rec.versions.reduce((m, v) => Math.max(m, v.id), 0) + 1;
      const dir = join('plugins', p.slug, String(id));
      await mkdir(this.abs(dir), { recursive: true });
      const zipFile = join(dir, `${p.slug}.${p.version}.zip`);
      await copyFile(p.zipPath, this.abs(zipFile));
      let reportFile: string | null = null;
      if (p.reportPath) {
        try {
          reportFile = join(dir, 'report.md');
          await copyFile(p.reportPath, this.abs(reportFile));
        } catch {
          reportFile = null;
        }
      }
      const v: VersionRecord = {
        id,
        version: p.version,
        kind: p.kind,
        description: p.description.slice(0, 4000),
        ai: p.ai,
        costUsd: p.costUsd ?? 0,
        iterations: p.iterations ?? 0,
        auditFindings: p.auditFindings ?? null,
        provides: p.provides ?? '',
        createdAt: now,
        zipFile,
        reportFile,
      };
      rec.versions.push(v);
      rec.currentVersionId = id;
      rec.updatedAt = now;
      pushActivity(rec, true, `${p.kind}: v${p.version} recorded (version #${id}).`);
      await this.save(data);
      return v;
    });
  }

  /** Attach a full-source snapshot to a version (copied into the registry). */
  async setVersionSource(slug: string, versionId: number, sourceZipPath: string): Promise<void> {
    await this.exclusive(async (data) => {
      const v = data.plugins[slug]?.versions.find((x) => x.id === versionId);
      if (!v) return;
      const rel = join('plugins', slug, String(versionId), 'source.zip');
      await copyFile(sourceZipPath, this.abs(rel));
      v.sourceFile = rel;
      await this.save(data);
    });
  }

  /** Record that a version was published to GitHub. */
  async recordGithub(slug: string, repo: Omit<GithubPublish, 'releases'>, release: GithubRelease): Promise<void> {
    await this.exclusive(async (data) => {
      const rec = data.plugins[slug];
      if (!rec) return;
      rec.published = rec.published ?? {};
      const gh = rec.published.github ?? { ...repo, releases: [] };
      Object.assign(gh, repo);
      gh.releases = gh.releases.filter((r) => r.tag !== release.tag);
      gh.releases.push(release);
      rec.published.github = gh;
      pushActivity(rec, true, `Published v${release.version} to GitHub (${repo.owner}/${repo.repo}, tag ${release.tag}).`);
      rec.updatedAt = new Date().toISOString();
      await this.save(data);
    });
  }

  /** Note a failed or informational event on a plugin's activity line (no-op for unknown slugs). */
  async addActivity(slug: string, ok: boolean, message: string): Promise<void> {
    await this.exclusive(async (data) => {
      const rec = data.plugins[slug];
      if (!rec) return;
      pushActivity(rec, ok, message);
      rec.updatedAt = new Date().toISOString();
      await this.save(data);
    });
  }

  /** Edit the CRM fields. Unknown or invalid fields are ignored. */
  async update(slug: string, fields: Record<string, unknown>): Promise<PluginRecord | null> {
    return this.exclusive(async (data) => {
      const rec = data.plugins[slug];
      if (!rec) return null;
      const str = (v: unknown, max: number) => String(v ?? '').slice(0, max).trim();
      if (fields.name !== undefined) rec.name = str(fields.name, 200) || rec.name;
      if (fields.client !== undefined) rec.client = str(fields.client, 200);
      if (fields.notes !== undefined) rec.notes = String(fields.notes ?? '').slice(0, 20000);
      if (fields.liveUrl !== undefined) {
        const u = str(fields.liveUrl, 500);
        rec.liveUrl = u === '' || /^https?:\/\//i.test(u) ? u : rec.liveUrl;
      }
      if (fields.status !== undefined && (STATUSES as readonly string[]).includes(String(fields.status))) {
        rec.status = fields.status as Status;
      }
      if (fields.doNotPublish !== undefined) rec.doNotPublish = Boolean(fields.doNotPublish);
      rec.updatedAt = new Date().toISOString();
      await this.save(data);
      return rec;
    });
  }

  /** Make an earlier version current again (edits then start from it). */
  async restore(slug: string, versionId: number): Promise<PluginRecord | null> {
    return this.exclusive(async (data) => {
      const rec = data.plugins[slug];
      const v = rec?.versions.find((x) => x.id === versionId);
      if (!rec || !v) return null;
      rec.currentVersionId = v.id;
      pushActivity(rec, true, `Restored version #${v.id} (v${v.version}) as current.`);
      rec.updatedAt = new Date().toISOString();
      await this.save(data);
      return rec;
    });
  }

  /** Remove a plugin record and every stored version. */
  async remove(slug: string): Promise<boolean> {
    if (!validSlug(slug)) return false;
    return this.exclusive(async (data) => {
      if (!data.plugins[slug]) return false;
      delete data.plugins[slug];
      await this.save(data);
      await rm(this.abs(join('plugins', slug)), { recursive: true, force: true });
      return true;
    });
  }

  /** Absolute path of a version's file, or null. */
  async versionFile(slug: string, versionId: number, which: 'zip' | 'report' | 'source'): Promise<string | null> {
    const rec = await this.get(slug);
    const v = rec?.versions.find((x) => x.id === versionId);
    if (!v) return null;
    const rel = which === 'zip' ? v.zipFile : which === 'source' ? v.sourceFile ?? null : v.reportFile;
    return rel ? this.abs(rel) : null;
  }
}

function pushActivity(rec: PluginRecord, ok: boolean, message: string): void {
  rec.activity.push({ at: new Date().toISOString(), ok, message: message.slice(0, 500) });
  if (rec.activity.length > MAX_ACTIVITY) rec.activity = rec.activity.slice(-MAX_ACTIVITY);
}

