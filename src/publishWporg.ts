/**
 * Push one My Plugins version to an APPROVED plugin's WordPress.org SVN repository.
 *
 *   tsx src/run.ts wporg-push <slug> <versionId> <wporgSlug>
 *   env (builder service machine only, never WordPress):
 *     AIWPB_WPORG_SVN_USER, AIWPB_WPORG_SVN_PASSWORD   your WordPress.org username + SVN password
 *     AIWPB_WPORG_SVN_BASE   default https://plugins.svn.wordpress.org (a file:// repo for testing)
 *     AIWPB_SVN_BIN          svn binary (default: svn on PATH, then ~/miniconda3/envs/aiwpb-svn/bin/svn)
 *
 * Steps: refuse do-not-publish → re-run every gate + spec coverage on the version's installable package →
 * check readme Stable tag == version → refuse if tags/<version> already exists → replace trunk with the
 * package, update /assets (banners, icons, screenshots), svn cp trunk → tags/<version>, one commit.
 * The password reaches svn only on stdin (--password-from-stdin), never argv or disk (--no-auth-cache).
 */
import { mkdir, rm, readdir, cp, readFile, access } from 'node:fs/promises';
import { join } from 'node:path';
import { Registry } from './registry.js';
import { runPipeline } from './pipeline.js';
import { withSpecCoverage } from './specCoverage.js';
import { renderTerminal } from './report.js';
import { exec, type ExecResult } from './util/exec.js';
import { emitResult } from './resultFile.js';
import { dockerAvailable } from './wpEnv.js';
import { headerField, readmeIssues, latestTestedUpTo, compareVersions } from './wporg.js';

interface Env {
  repoRoot: string;
  harnessDir: string;
}

async function exists(p: string): Promise<boolean> {
  try {
    await access(p);
    return true;
  } catch {
    return false;
  }
}

/** Find an svn binary: explicit, then PATH, then the conda env set up for this tool. */
export async function svnBinary(): Promise<string | null> {
  const candidates = [process.env.AIWPB_SVN_BIN, 'svn', join(process.env.HOME || '/home/snavazio', 'miniconda3/envs/aiwpb-svn/bin/svn')].filter(Boolean) as string[];
  for (const c of candidates) {
    const r = await exec(c, ['--version', '--quiet'], { timeoutMs: 10_000 });
    if (r.code === 0) return c;
  }
  return null;
}

export async function runPublishWporg(args: string[], env: Env): Promise<number> {
  const [slug, versionIdRaw, wporgSlugArg] = args;
  const wporgSlug = wporgSlugArg || slug;
  const fail = async (error: string, code = 1): Promise<number> => {
    console.error(`\n✖ ${error}`);
    await emitResult({ ok: false, engine: 'local', slug, error });
    return code;
  };
  if (!slug || !versionIdRaw) return fail('Usage: tsx src/run.ts wporg-push <slug> <versionId> [wporgSlug]', 2);
  if (!/^[a-z0-9][a-z0-9-]{1,80}$/.test(wporgSlug)) return fail(`Invalid WordPress.org slug "${wporgSlug}".`, 2);
  const base = (process.env.AIWPB_WPORG_SVN_BASE || 'https://plugins.svn.wordpress.org').replace(/\/+$/, '');
  const isLocalTest = base.startsWith('file://');
  const user = process.env.AIWPB_WPORG_SVN_USER || '';
  const password = process.env.AIWPB_WPORG_SVN_PASSWORD || '';
  if (!isLocalTest && (!user || !password)) {
    return fail('WordPress.org SVN credentials are not set on the builder service. On thing1, start the service with AIWPB_WPORG_SVN_USER and AIWPB_WPORG_SVN_PASSWORD.', 2);
  }
  const svn = await svnBinary();
  if (!svn) return fail('svn is not installed on the builder service machine.', 2);

  const reg = new Registry(env.repoRoot);
  const rec = await reg.get(slug);
  const v = rec?.versions.find((x) => x.id === Number(versionIdRaw));
  if (!rec || !v) return fail(`Plugin "${slug}" version #${versionIdRaw} is not in My Plugins.`);
  if (rec.doNotPublish) return fail(`"${rec.name}" is marked do-not-publish.`);

  const work = join(env.repoRoot, 'build', `.wporg-push-${Date.now()}`);
  await mkdir(work, { recursive: true });
  const auth = isLocalTest ? [] : ['--username', user, '--password-from-stdin', '--no-auth-cache'];
  const run = (a: string[], cwd = work, withAuth = false): Promise<ExecResult> =>
    exec(svn, ['--non-interactive', ...a, ...(withAuth ? auth : [])], { cwd, timeoutMs: 600_000, input: withAuth && !isLocalTest ? password + '\n' : undefined });
  const must = async (a: string[], cwd = work, withAuth = false): Promise<ExecResult> => {
    const r = await run(a, cwd, withAuth);
    if (r.code !== 0) throw new Error(`svn ${a[0]} failed: ${(r.stderr || r.stdout).trim().slice(-500)}`);
    return r;
  };

  try {
    // 1. The installable package (what users download), unpacked.
    const pkgDir = join(work, 'pkg');
    const ex = await exec('php', [join(env.harnessDir, 'bin', 'unzip.php'), reg.abs(v.zipFile), pkgDir], { timeoutMs: 60_000 });
    const pluginDir = join(pkgDir, slug);
    if (ex.code !== 0 || !(await exists(pluginDir))) return fail('Could not unpack the stored version.');
    const readme = await readFile(join(pluginDir, 'readme.txt'), 'utf8').catch(() => '');
    if (headerField(readme, 'Stable tag') !== v.version) {
      return fail(`readme.txt "Stable tag" is "${headerField(readme, 'Stable tag')}", not ${v.version}. Use "Prepare for WordPress.org" on this version first.`);
    }
    // The readme must meet the directory's rules — i.e. this version went through "Prepare for WordPress.org".
    const wporgUser = process.env.AIWPB_WPORG_USER || user || 'snavazio';
    const issues = await readmeIssues(pluginDir, { version: v.version, username: wporgUser, testedUpTo: await latestTestedUpTo() });
    if (issues.length) {
      return fail(`v${v.version} is not prepared for WordPress.org (${issues.join(' ')}). Run "Prepare for WordPress.org" on it and push the version that creates.`);
    }
    console.log(`▶ Pushing ${rec.name} v${v.version} to ${base}/${wporgSlug}`);

    // 2. Re-verify.
    if (!(await dockerAvailable())) return fail('Docker is not available, so the checks cannot run. Nothing was pushed.');
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
      return fail('Not pushed: this version does not pass every check.');
    }

    // 3. Sparse checkout: trunk + assets in full, tags one level deep.
    const wc = join(work, 'wc');
    await must(['checkout', '--depth', 'immediates', `${base}/${wporgSlug}`, wc], work, true);
    for (const d of ['trunk', 'assets', 'tags']) {
      if (!(await exists(join(wc, d)))) await must(['mkdir', d], wc);
    }
    await must(['update', '--set-depth', 'infinity', 'trunk', 'assets'], wc, true);
    await must(['update', '--set-depth', 'immediates', 'tags'], wc, true);
    if (await exists(join(wc, 'tags', v.version))) {
      return fail(`tags/${v.version} already exists on WordPress.org. Make a new version (Fix/Change/…) and prepare it first.`);
    }
    // Never move trunk backwards: an older version would replace the live code for every user.
    const tags = (await readdir(join(wc, 'tags'))).filter((t) => /^\d+(\.\d+)*$/.test(t));
    const newest = tags.sort(compareVersions).at(-1);
    if (newest && compareVersions(v.version, newest) <= 0) {
      return fail(`v${v.version} is not newer than v${newest}, which is already on WordPress.org. Pushing it would roll the live plugin back.`);
    }

    // 4. trunk := the package. Removed files become svn deletions.
    for (const e of await readdir(join(wc, 'trunk'))) if (e !== '.svn') await rm(join(wc, 'trunk', e), { recursive: true, force: true });
    await cp(pluginDir, join(wc, 'trunk'), { recursive: true });

    // 5. assets := this version's artwork + screenshots (when it has them).
    if (v.assetsFile && (await exists(reg.abs(v.assetsFile)))) {
      const aDir = join(work, 'assets-unzipped');
      await exec('php', [join(env.harnessDir, 'bin', 'unzip.php'), reg.abs(v.assetsFile), aDir], { timeoutMs: 60_000 });
      if (await exists(join(aDir, 'assets'))) await cp(join(aDir, 'assets'), join(wc, 'assets'), { recursive: true, force: true });
    }

    // 6. Stage adds + deletions, PNG mime types, tag, commit.
    await must(['add', '--force', '--parents', 'trunk', 'assets'], wc);
    const st = await must(['status'], wc);
    for (const line of st.stdout.split('\n')) {
      if (line.startsWith('!')) await must(['delete', '--force', line.slice(8).trim()], wc);
    }
    for (const f of await readdir(join(wc, 'assets')).catch(() => [] as string[])) {
      if (f.endsWith('.png')) await run(['propset', 'svn:mime-type', 'image/png', join('assets', f)], wc);
    }
    await must(['copy', 'trunk', join('tags', v.version)], wc);
    const commit = await must(['commit', '-m', `Release ${v.version}`], wc, true);
    const revision = commit.stdout.match(/Committed revision (\d+)/)?.[1] ?? '';
    console.log(`  committed revision ${revision}: trunk, tags/${v.version}${v.assetsFile ? ', assets' : ''}`);

    await emitResult({
      ok: true,
      engine: 'local',
      slug,
      pluginName: rec.name,
      version: v.version,
      wporgPush: { versionId: v.id, version: v.version, revision, wporgSlug },
    });
    console.log(`\n✔ PUSHED: ${rec.name} ${v.version} → https://wordpress.org/plugins/${wporgSlug}/`);
    return 0;
  } catch (e) {
    return fail(e instanceof Error ? e.message : String(e));
  } finally {
    await rm(work, { recursive: true, force: true });
  }
}
