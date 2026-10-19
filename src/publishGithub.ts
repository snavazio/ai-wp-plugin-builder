/**
 * Publish one registry version of a plugin to GitHub.
 *
 *   tsx src/run.ts publish-github <slug> <versionId>
 *   env: GITHUB_TOKEN (required), AIWPB_GH_OWNER (default: the token's user), AIWPB_GH_PRIVATE (1/0, default 1),
 *        AIWPB_RESULT_FILE (set by the service)
 *
 * Steps: refuse do-not-publish plugins → re-run all gates + spec coverage on the version → create the repo
 * (private by default) if missing → refuse if the version's tag already exists → commit the version's full
 * source to main, tag v<version>, push → create a GitHub Release with the installable .zip attached.
 *
 * The token never appears on a command line or on disk: git receives it through GIT_CONFIG_* environment
 * variables as an HTTP header, and the GitHub API calls send it as a header.
 */
import { mkdir, rm, readdir, cp, readFile, writeFile, access } from 'node:fs/promises';
import { join } from 'node:path';
import { Registry, type PluginRecord, type VersionRecord } from './registry.js';
import { runPipeline } from './pipeline.js';
import { withSpecCoverage } from './specCoverage.js';
import { renderTerminal } from './report.js';
import { exec } from './util/exec.js';
import { emitResult } from './resultFile.js';
import { dockerAvailable } from './wpEnv.js';

interface Env {
  repoRoot: string;
  harnessDir: string;
}

const API = 'https://api.github.com';
const UPLOADS = 'https://uploads.github.com';

async function exists(p: string): Promise<boolean> {
  try {
    await access(p);
    return true;
  } catch {
    return false;
  }
}

/** Minimal GitHub REST client. Throws with GitHub's own message on errors (never includes the token). */
function github(token: string) {
  const headers = {
    authorization: `Bearer ${token}`,
    accept: 'application/vnd.github+json',
    'x-github-api-version': '2022-11-28',
    'user-agent': 'ai-wp-plugin-builder',
  };
  async function call(method: string, url: string, body?: unknown, extra: Record<string, string> = {}): Promise<{ status: number; data: any }> {
    const res = await fetch(url, {
      method,
      headers: { ...headers, ...extra, ...(body !== undefined && !(body instanceof Uint8Array) ? { 'content-type': 'application/json' } : {}) },
      body: body === undefined ? undefined : body instanceof Uint8Array ? body : JSON.stringify(body),
      redirect: 'error',
      signal: AbortSignal.timeout(120_000),
    });
    const text = await res.text();
    let data: any = null;
    try {
      data = text ? JSON.parse(text) : null;
    } catch {
      data = { message: text.slice(0, 300) };
    }
    return { status: res.status, data };
  }
  const fail = (what: string, r: { status: number; data: any }): never => {
    const extra = Array.isArray(r.data?.errors) ? ' — ' + r.data.errors.map((e: any) => e.message || e.code).join('; ') : '';
    throw new Error(`GitHub ${what} failed (HTTP ${r.status}): ${r.data?.message ?? 'no details'}${extra}`);
  };
  return { call, fail };
}

/** git with the token passed as an HTTP header via environment config (not argv, not .git/config). */
function gitRunner(cwd: string, token: string) {
  const env: NodeJS.ProcessEnv = {
    ...process.env,
    GIT_TERMINAL_PROMPT: '0',
    GIT_CONFIG_COUNT: '1',
    GIT_CONFIG_KEY_0: 'http.https://github.com/.extraheader',
    GIT_CONFIG_VALUE_0: `AUTHORIZATION: basic ${Buffer.from(`x-access-token:${token}`).toString('base64')}`,
  };
  return async (args: string[], allowFail = false) => {
    const r = await exec('git', args, { cwd, env, timeoutMs: 180_000 });
    if (r.code !== 0 && !allowFail) throw new Error(`git ${args[0]} failed: ${(r.stderr || r.stdout).trim().slice(-600)}`);
    return r;
  };
}

function readmeFor(rec: PluginRecord, v: VersionRecord, readmeTxt: string): string {
  const desc = readmeTxt.split('\n').find((l, i, all) => i > 0 && l.trim() && !/^[A-Za-z ]+:/.test(l) && !l.startsWith('==') && all[i - 1].trim() === '')?.trim() ?? '';
  return [
    `# ${rec.name}`,
    '',
    desc,
    '',
    `**Version:** ${v.version}`,
    '',
    '## Install',
    '',
    `Download \`${rec.slug}.${v.version}.zip\` from the latest release, then in WordPress go to Plugins → Add New → Upload Plugin.`,
    '',
    '---',
    '_Built and verified with AI WP Plugin Builder: every release passes PHP lint, WordPress Coding Standards, PHPStan, WordPress Plugin Check (security), activation in a real WordPress, the PHPUnit smoke test, version consistency and spec coverage._',
    '',
  ].join('\n');
}

function releaseNotes(rec: PluginRecord, v: VersionRecord, checkedAt: string, hasSource: boolean): string {
  const desc = (v.description || '').trim();
  const quoted = desc.length > 2000 ? desc.slice(0, 2000) + '…' : desc;
  return [
    `**${rec.name} v${v.version}** — ${v.kind}${v.ai ? ` · AI: ${v.ai}` : ''}`,
    '',
    `- Checks: all 8 gates + spec coverage re-run and passed at publish (${checkedAt}).`,
    `- Security auditor: ${v.auditFindings === null || v.auditFindings === undefined ? 'not run for this version (local engine or import)' : `${v.auditFindings} low-severity note(s) remaining, no medium/high findings`}.`,
    hasSource ? '- Source: full source including tests.' : '- Source: the installable package (this version was imported, so no test files were kept).',
    '',
    '### What was asked',
    '',
    quoted ? quoted.split('\n').map((l) => `> ${l}`).join('\n') : '_No description recorded._',
    '',
    '### Install',
    '',
    `Download \`${rec.slug}.${v.version}.zip\` below, then in WordPress: Plugins → Add New → Upload Plugin.`,
  ].join('\n');
}

export async function runPublishGithub(args: string[], env: Env): Promise<number> {
  const [slug, versionIdRaw] = args;
  const token = process.env.GITHUB_TOKEN || '';
  const fail = async (error: string, code = 1): Promise<number> => {
    console.error(`\n✖ ${error}`);
    await emitResult({ ok: false, engine: 'local', slug, error });
    return code;
  };
  if (!slug || !versionIdRaw) return fail('Usage: tsx src/run.ts publish-github <slug> <versionId>', 2);
  if (!token) return fail('No GitHub token. Save one under AI Plugin Builder → Settings → GitHub.', 2);

  const reg = new Registry(env.repoRoot);
  const rec = await reg.get(slug);
  const v = rec?.versions.find((x) => x.id === Number(versionIdRaw));
  if (!rec || !v) return fail(`Plugin "${slug}" version #${versionIdRaw} is not in My Plugins.`);
  if (rec.doNotPublish) return fail(`"${rec.name}" is marked do-not-publish. Untick that in its details first if you really want to publish it.`);

  const workDir = join(env.repoRoot, 'build', `.publish-${Date.now()}`);
  await mkdir(workDir, { recursive: true });
  try {
    // 1. Unpack the version (full source if we have it, else the installable zip).
    const sourceRel = v.sourceFile ?? null;
    const hasSource = !!sourceRel && (await exists(reg.abs(sourceRel)));
    const zipToUse = hasSource ? reg.abs(sourceRel!) : reg.abs(v.zipFile);
    const pkgZip = reg.abs(v.zipFile);
    console.log(`▶ Publishing ${rec.name} v${v.version} (version #${v.id}) — ${hasSource ? 'full source' : 'installable package only'}`);
    const unpacked = join(workDir, 'src');
    const ex = await exec('php', [join(env.harnessDir, 'bin', 'unzip.php'), zipToUse, unpacked], { timeoutMs: 60_000 });
    if (ex.code !== 0) return fail('Could not unpack the stored version.');
    const pluginDir = join(unpacked, slug);
    if (!(await exists(pluginDir))) return fail(`The stored .zip has no "${slug}/" folder.`);

    // 2. Re-verify: all gates + spec coverage. Nothing is published unless this passes.
    if (!(await dockerAvailable())) return fail('Docker is not available on the builder service, so the gates cannot run. Nothing was published.');
    console.log('▶ Re-running every check before publishing…');
    const pipe = await withSpecCoverage(
      await runPipeline(pluginDir, {
        repoRoot: env.repoRoot,
        harnessDir: env.harnessDir,
        onGate: (r) => process.stdout.write(`    [${r.skipped ? 'SKIP' : r.passed ? 'PASS' : 'FAIL'}] ${r.label}\n`),
      }),
      pluginDir,
    );
    if (!pipe.passed) {
      console.log(renderTerminal(pipe));
      const failed = pipe.results.filter((r) => !r.passed).map((r) => r.label).join(', ');
      return fail(`Not published: this version does not pass every check (${failed}). Fix it first.`);
    }
    const checkedAt = new Date().toISOString().replace('T', ' ').slice(0, 16) + ' UTC';

    // 3. Repo: find or create.
    const gh = github(token);
    const me = await gh.call('GET', `${API}/user`);
    if (me.status !== 200) gh.fail('token check', me);
    const login: string = me.data.login;
    const owner = (process.env.AIWPB_GH_OWNER || login).trim();
    // GitHub user/org names: letters, digits and single hyphens. It goes into API paths, so check it.
    if (!/^[A-Za-z0-9](?:[A-Za-z0-9-]{0,38})$/.test(owner)) return fail(`"${owner}" is not a valid GitHub account name.`);
    const wantPrivate = process.env.AIWPB_GH_PRIVATE !== '0';
    console.log(`▶ GitHub: signed in as ${login}; target ${owner}/${slug}`);
    let repo = await gh.call('GET', `${API}/repos/${owner}/${slug}`);
    if (repo.status === 404) {
      const createUrl = owner.toLowerCase() === login.toLowerCase() ? `${API}/user/repos` : `${API}/orgs/${owner}/repos`;
      repo = await gh.call('POST', createUrl, {
        name: slug,
        private: wantPrivate,
        description: (rec.versions.find((x) => x.provides)?.provides || rec.name).slice(0, 300),
        has_wiki: false,
        has_projects: false,
      });
      if (repo.status !== 201) gh.fail('creating the repository', repo);
      console.log(`  created ${repo.data.private ? 'private' : 'PUBLIC'} repo ${repo.data.html_url}`);
    } else if (repo.status !== 200) {
      gh.fail('looking up the repository', repo);
    } else {
      console.log(`  using existing ${repo.data.private ? 'private' : 'public'} repo ${repo.data.html_url}`);
    }
    const repoUrl: string = repo.data.html_url;
    const isPrivate: boolean = !!repo.data.private;
    const tag = `v${v.version}`;
    const tagCheck = await gh.call('GET', `${API}/repos/${owner}/${slug}/git/ref/tags/${encodeURIComponent(tag)}`);
    if (tagCheck.status === 200) {
      return fail(`${tag} is already on GitHub (${repoUrl}/releases/tag/${tag}). Make a new version (Fix/Change/…) before publishing again.`);
    }

    // 4. Commit the source to main, tag, push.
    const gitDir = join(workDir, 'repo');
    await mkdir(gitDir, { recursive: true });
    const git = gitRunner(gitDir, token);
    await git(['init', '-q', '-b', 'main']);
    await git(['remote', 'add', 'origin', `https://github.com/${owner}/${slug}.git`]);
    const fetched = await git(['fetch', '-q', '--depth', '50', 'origin', 'main'], true);
    if (fetched.code === 0) await git(['checkout', '-q', '-B', 'main', 'FETCH_HEAD']);
    for (const e of await readdir(gitDir)) if (e !== '.git') await rm(join(gitDir, e), { recursive: true, force: true });
    await cp(pluginDir, gitDir, { recursive: true });
    if (!(await exists(join(gitDir, 'README.md')))) {
      const readmeTxt = await readFile(join(gitDir, 'readme.txt'), 'utf8').catch(() => '');
      await writeFile(join(gitDir, 'README.md'), readmeFor(rec, v, readmeTxt), 'utf8');
    }
    if (!(await exists(join(gitDir, '.gitignore')))) {
      await writeFile(join(gitDir, '.gitignore'), 'vendor/\nnode_modules/\n.phpunit.result.cache\n', 'utf8');
    }
    await git(['add', '-A']);
    const firstLine = (v.description || '').split('\n').find((l) => l.trim())?.replace(/^(User|Assistant):\s*/, '').slice(0, 72) || v.kind;
    await git(['commit', '-q', '--allow-empty', '-m', `${tag} (${v.kind}): ${firstLine}`, '-m', `Published from AI WP Plugin Builder (version #${v.id}). All checks passed at ${checkedAt}.`]);
    await git(['tag', '-a', tag, '-m', `${rec.name} ${tag}`]);
    await git(['push', '-q', 'origin', 'main', `refs/tags/${tag}`]);
    const commit = (await git(['rev-parse', 'HEAD'])).stdout.trim();
    console.log(`  pushed ${commit.slice(0, 7)} to main and tag ${tag}`);

    // 5. Release + installable zip.
    const rel = await gh.call('POST', `${API}/repos/${owner}/${slug}/releases`, {
      tag_name: tag,
      name: `${rec.name} ${tag}`,
      body: releaseNotes(rec, v, checkedAt, hasSource),
    });
    if (rel.status !== 201) gh.fail('creating the release', rel);
    const assetName = `${slug}.${v.version}.zip`;
    const up = await gh.call(
      'POST',
      `${UPLOADS}/repos/${owner}/${slug}/releases/${rel.data.id}/assets?name=${encodeURIComponent(assetName)}`,
      new Uint8Array(await readFile(pkgZip)),
      { 'content-type': 'application/zip' },
    );
    if (up.status !== 201) gh.fail('uploading the .zip to the release', up);
    console.log(`  release ${rel.data.html_url} (+ ${assetName})`);

    await emitResult({
      ok: true,
      engine: 'local',
      slug,
      pluginName: rec.name,
      version: v.version,
      github: { owner, repo: slug, url: repoUrl, private: isPrivate, tag, commit, releaseUrl: rel.data.html_url, versionId: v.id },
    });
    console.log(`\n✔ PUBLISHED: ${rec.name} ${tag} → ${rel.data.html_url}`);
    return 0;
  } catch (e) {
    return fail(e instanceof Error ? e.message : String(e));
  } finally {
    await rm(workDir, { recursive: true, force: true });
  }
}

