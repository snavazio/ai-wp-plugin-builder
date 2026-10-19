/**
 * Prepare a My Plugins version for the WordPress.org plugin directory.
 *
 *   tsx src/run.ts wporg-prepare <slug> <versionId>
 *   env: AIWPB_WPORG_USER (WordPress.org username, default "snavazio"), AIWPB_RESULT_FILE (set by the service)
 *
 * Produces a new version (patch bump) whose readme meets the directory's rules, plus the directory artwork
 * (banners, icons) and real screenshots taken in the local sandbox. Every gate + spec coverage + Plugin
 * Check at directory strictness (all categories; any error or trademark warning fails) must pass. Nothing is
 * sent to WordPress.org: the first submission is a manual upload followed by a human review.
 */
import { mkdir, rm, cp, readFile, writeFile, access, readdir } from 'node:fs/promises';
import { join } from 'node:path';
import { Registry } from './registry.js';
import { runPipeline } from './pipeline.js';
import { withSpecCoverage } from './specCoverage.js';
import { renderMarkdown, renderTerminal } from './report.js';
import { exec } from './util/exec.js';
import { emptyDir } from './util/fs.js';
import { emitResult } from './resultFile.js';
import { WpEnv, dockerAvailable } from './wpEnv.js';
import {
  latestTestedUpTo,
  slugStatus,
  bumpPatch,
  mainFile,
  headerField,
  setVersion,
  prepareReadme,
  readmeIssues,
  nameIssues,
  strictPluginCheck,
  discoverScreens,
} from './wporg.js';

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

export async function runPrepareWporg(args: string[], env: Env): Promise<number> {
  const [slug, versionIdRaw] = args;
  const username = (process.env.AIWPB_WPORG_USER || 'snavazio').trim();
  const fail = async (error: string, code = 1): Promise<number> => {
    console.error(`\n✖ ${error}`);
    await emitResult({ ok: false, engine: 'local', slug, error });
    return code;
  };
  if (!slug || !versionIdRaw) return fail('Usage: tsx src/run.ts wporg-prepare <slug> <versionId>', 2);
  if (!/^[A-Za-z0-9-]{1,50}$/.test(username)) return fail(`"${username}" is not a valid WordPress.org username.`, 2);

  const reg = new Registry(env.repoRoot);
  const rec = await reg.get(slug);
  const v = rec?.versions.find((x) => x.id === Number(versionIdRaw));
  if (!rec || !v) return fail(`Plugin "${slug}" version #${versionIdRaw} is not in My Plugins.`);
  if (rec.doNotPublish) return fail(`"${rec.name}" is marked do-not-publish.`);
  if (!(await dockerAvailable())) return fail('Docker is not available, so the checks cannot run.');

  const startTs = Date.now();
  const lines: string[] = [];
  const note = (s: string) => {
    console.log(s);
    lines.push(s);
  };

  // 1. Fresh working copy of the version (full source when we have it).
  const pluginDir = join(env.repoRoot, 'build', slug);
  const work = join(env.repoRoot, 'build', `.wporg-${Date.now()}`);
  await emptyDir(pluginDir); // keep the folder: it is bind-mounted into the wp-env sandbox
  await mkdir(work, { recursive: true });
  try {
    const src = v.sourceFile && (await exists(reg.abs(v.sourceFile))) ? reg.abs(v.sourceFile) : reg.abs(v.zipFile);
    const ex = await exec('php', [join(env.harnessDir, 'bin', 'unzip.php'), src, work], { timeoutMs: 60_000 });
    if (ex.code !== 0 || !(await exists(join(work, slug)))) return fail('Could not unpack the stored version.');
    await cp(join(work, slug), pluginDir, { recursive: true });

    const main = await mainFile(pluginDir);
    if (!main) return fail('No main plugin file found.');
    const mainSrc = await readFile(main, 'utf8');
    const name = headerField(mainSrc, 'Plugin Name') || rec.name;
    const oldVersion = headerField(mainSrc, 'Version') || v.version;
    const newVersion = bumpPatch(oldVersion);
    const testedUpTo = await latestTestedUpTo();
    note(`▶ Preparing ${name} for WordPress.org: v${oldVersion} → v${newVersion} (username ${username}, tested up to ${testedUpTo})`);

    // 2. Name + slug.
    const blockers: string[] = [];
    for (const n of nameIssues(name, slug)) blockers.push(n);
    const wporgSlug = rec.published?.wporg?.slug || slug;
    const alreadyOurs = rec.published?.wporg?.status === 'approved';
    const st = alreadyOurs ? { state: 'free' as const } : await slugStatus(wporgSlug);
    if (st.state === 'taken') blockers.push(`The slug "${wporgSlug}" is already used in the directory by "${st.name}". The reviewers will assign a different slug, or rename the plugin.`);
    note(`  slug "${wporgSlug}": ${alreadyOurs ? 'yours (approved)' : st.state === 'free' ? 'available' : st.state === 'taken' ? 'TAKEN' : 'could not check'}`);

    // 3. Version bump + readme (screenshot captions are filled in after the screenshots exist).
    await setVersion(pluginDir, oldVersion, newVersion);
    const readmeOpts = {
      name,
      username,
      testedUpTo,
      version: newVersion,
      requiresWp: headerField(mainSrc, 'Requires at least') || '6.0',
      requiresPhp: headerField(mainSrc, 'Requires PHP') || '7.4',
      provides: v.provides || '',
      screenshots: [] as string[],
      locations: [] as string[],
      changelogLine: 'Prepared for the WordPress.org plugin directory: readme and assets updated.',
    };
    await prepareReadme(pluginDir, readmeOpts);

    // 4. Every gate + spec coverage (this also installs + activates it in the sandbox).
    const wpEnv = new WpEnv(env.repoRoot, pluginDir, slug, env.harnessDir);
    note('▶ Running every check…');
    const pipe = await withSpecCoverage(
      await runPipeline(pluginDir, {
        repoRoot: env.repoRoot,
        harnessDir: env.harnessDir,
        wpEnv,
        onGate: (r) => process.stdout.write(`    [${r.skipped ? 'SKIP' : r.passed ? 'PASS' : 'FAIL'}] ${r.label}\n`),
      }),
      pluginDir,
    );
    if (!pipe.passed) {
      console.log(renderTerminal(pipe));
      return fail(`Not ready: the gates failed (${pipe.results.filter((r) => !r.passed).map((r) => r.label).join(', ')}).`);
    }

    // 5. Plugin Check at directory strictness.
    note('▶ Plugin Check, all categories (directory strictness)…');
    const pc = await strictPluginCheck(wpEnv, slug);
    note(`  ${pc.errors.length} error(s), ${pc.warnings.length} warning(s)`);
    for (const e of pc.errors.slice(0, 20)) note(`    ✖ ${e}`);
    blockers.push(...pc.errors.map((e) => `Plugin Check: ${e}`));

    // 6. Screenshots in the sandbox, then captions into the readme.
    note('▶ Taking screenshots in the sandbox…');
    await wpEnv.wp(['option', 'update', 'blogname', 'Demo Site']);
    const shots = await discoverScreens(wpEnv, slug, pluginDir);
    readmeOpts.locations = shots.map((s) => s.location);
    const assetsDir = join(work, 'assets');
    await mkdir(assetsDir, { recursive: true });
    const ss = await exec('python3', [join(env.harnessDir, 'bin', 'wporg-screens.py'), 'http://localhost:8888', assetsDir, JSON.stringify(shots)], { timeoutMs: 180_000 });
    const captions: string[] = [];
    if (ss.code === 0) {
      for (const l of ss.stdout.trim().split('\n')) {
        const [, caption] = l.split('\t');
        if (caption !== undefined) captions.push(caption || 'Screenshot');
      }
      note(`  ${captions.length} screenshot(s): ${captions.join(' | ')}`);
    } else {
      note(`  screenshots could not be taken (${(ss.stderr || ss.stdout).trim().slice(-200)}); the readme keeps no Screenshots section.`);
    }
    readmeOpts.screenshots = captions;
    await prepareReadme(pluginDir, readmeOpts);
    for (const i of await readmeIssues(pluginDir, { version: newVersion, username, testedUpTo })) blockers.push(`readme.txt: ${i}`);

    // 7. Artwork.
    const tagline = (v.provides || '').split(' · ')[0]?.slice(0, 90) || '';
    const art = await exec('python3', [join(env.harnessDir, 'bin', 'wporg-assets.py'), assetsDir, slug, name, tagline], { timeoutMs: 60_000 });
    note(art.code === 0 ? '▶ Artwork: 2 banners + 2 icons generated.' : `  artwork failed: ${(art.stderr || art.stdout).trim().slice(-200)}`);

    // 8. Package: installable zip + assets zip.
    await mkdir(join(env.repoRoot, 'dist'), { recursive: true });
    const zipPath = join(env.repoRoot, 'dist', `${slug}.${newVersion}.zip`);
    const pkg = await exec('php', [join(env.harnessDir, 'bin', 'package.php'), pluginDir, slug, zipPath], { timeoutMs: 60_000 });
    if (pkg.code !== 0) return fail('Packaging failed.');
    const assetsZip = join(env.repoRoot, 'dist', `${slug}.${newVersion}-wporg-assets.zip`);
    const az = await exec('php', [join(env.harnessDir, 'bin', 'zipdir.php'), assetsDir, 'assets', assetsZip], { timeoutMs: 60_000 });
    if (az.code !== 0) return fail('Could not zip the assets.');
    const assetNames = (await readdir(assetsDir)).sort();

    // 9. Report + checklist.
    const ready = blockers.length === 0;
    const reportPath = join(env.repoRoot, 'dist', `${slug}-wporg-report.md`);
    const report = [
      `# WordPress.org preparation: ${name} v${newVersion}`,
      '',
      `- **Result:** ${ready ? '✅ ready to submit' : `❌ ${blockers.length} problem(s) to fix before submitting`}`,
      `- **WordPress.org username (Contributors):** ${username}  ·  **Tested up to:** ${testedUpTo}  ·  **Slug:** ${wporgSlug}`,
      `- **Installable zip:** \`${slug}.${newVersion}.zip\`  ·  **Directory assets:** ${assetNames.join(', ')}`,
      `- **Took:** ${((Date.now() - startTs) / 60000).toFixed(1)} min`,
      '',
      ...(blockers.length ? ['## Fix before submitting', '', ...blockers.map((b) => `- ${b}`), ''] : []),
      '## How to submit (manual — WordPress.org has no API for this)',
      '',
      `1. Sign in at https://wordpress.org/plugins/developers/add/ as **${username}**.`,
      `2. Upload \`${slug}.${newVersion}.zip\` and accept the guidelines.`,
      '3. Wait for the review email (days to a few weeks). Reply to it from the same address if they ask for changes.',
      '4. In My Plugins, click **I submitted it**. When the plugin goes live, **Check approval** marks it approved, and **Push to WordPress.org** sends updates (and these assets) through SVN.',
      '',
      '## What the tools could not check',
      '',
      '- The reviewers also judge things no tool checks: whether the plugin is useful and complete, ' +
        'the quality of the description, external services disclosure, and uniqueness of the name.',
      '',
      '## Plugin Check warnings (not blocking)',
      '',
      ...(pc.warnings.length ? pc.warnings.slice(0, 40).map((w) => `- ${w}`) : ['- none']),
      '',
      renderMarkdown(pipe),
    ].join('\n');
    await writeFile(reportPath, report + '\n', 'utf8');

    await emitResult({
      ok: ready,
      engine: 'local',
      slug,
      pluginName: name,
      version: newVersion,
      zip: ready ? zipPath : null,
      report: reportPath,
      iterations: 0,
      provides: v.provides,
      error: ready ? undefined : `Not ready for WordPress.org: ${blockers.length} problem(s) — see the report.`,
      wporg: { assetsZip, wporgSlug, testedUpTo, username, blockers },
    });
    note(ready ? `\n✔ READY: ${slug}.${newVersion}.zip + assets — submit at https://wordpress.org/plugins/developers/add/` : `\n✖ ${blockers.length} problem(s) to fix first:\n  - ${blockers.join('\n  - ')}`);
    return ready ? 0 : 1;
  } catch (e) {
    return fail(e instanceof Error ? e.message : String(e));
  } finally {
    await rm(work, { recursive: true, force: true });
  }
}
