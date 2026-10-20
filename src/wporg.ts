/**
 * WordPress.org plugin-directory helpers: readme preparation + validation, version bump, slug availability,
 * strict Plugin Check, and screenshot discovery. Used by prepareWporg.ts (submission package) and
 * publishWporg.ts (SVN updates after approval).
 *
 * The directory's first submission is manual (a human review); these helpers make the package as close to
 * review-ready as tools can check, and say plainly what they cannot check.
 */
import { readFile, writeFile } from 'node:fs/promises';
import { join } from 'node:path';
import { findPhpFiles } from './util/exec.js';
import type { WpEnv } from './wpEnv.js';

export const DEFAULT_TESTED_UP_TO = '7.1';

/** Current WordPress major.minor for "Tested up to" (falls back to DEFAULT_TESTED_UP_TO offline). */
export async function latestTestedUpTo(): Promise<string> {
  try {
    const res = await fetch('https://api.wordpress.org/core/version-check/1.7/', { signal: AbortSignal.timeout(15_000) });
    const data = (await res.json()) as { offers?: Array<{ current?: string }> };
    const v = data.offers?.[0]?.current ?? '';
    const m = v.match(/^(\d+\.\d+)/);
    return m ? m[1] : DEFAULT_TESTED_UP_TO;
  } catch {
    return DEFAULT_TESTED_UP_TO;
  }
}

/** Whether a slug is free in the plugin directory. `unknown` when the API cannot be reached. */
export async function slugStatus(slug: string): Promise<{ state: 'free' | 'taken' | 'unknown'; name?: string }> {
  try {
    const res = await fetch(`https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&slug=${encodeURIComponent(slug)}`, {
      signal: AbortSignal.timeout(15_000),
    });
    const data = (await res.json()) as { error?: string; name?: string };
    if (data.error) return { state: 'free' };
    return { state: 'taken', name: data.name };
  } catch {
    return { state: 'unknown' };
  }
}

/** Compare dotted versions numerically: <0 if a<b, 0 if equal, >0 if a>b. */
export function compareVersions(a: string, b: string): number {
  const pa = a.split('.').map((x) => parseInt(x, 10) || 0);
  const pb = b.split('.').map((x) => parseInt(x, 10) || 0);
  for (let i = 0; i < Math.max(pa.length, pb.length); i++) {
    const d = (pa[i] ?? 0) - (pb[i] ?? 0);
    if (d !== 0) return d;
  }
  return 0;
}

export function bumpPatch(v: string): string {
  const m = v.match(/^(\d+)\.(\d+)(?:\.(\d+))?$/);
  return m ? `${m[1]}.${m[2]}.${parseInt(m[3] ?? '0', 10) + 1}` : v;
}

/** Find the main plugin file (the PHP file with a "Plugin Name:" header). */
export async function mainFile(pluginDir: string): Promise<string | null> {
  for (const f of await findPhpFiles(pluginDir)) {
    if (/\/tests\//.test(f)) continue;
    const src = await readFile(f, 'utf8');
    if (/^[ \t/*#@]*Plugin Name\s*:/im.test(src)) return f;
  }
  return null;
}

export function headerField(src: string, field: string): string {
  const re = new RegExp(`^[ \\t/*#@]*${field.replace(/ /g, '[ \\t]')}\\s*:\\s*(.+)$`, 'im');
  return src.match(re)?.[1]?.trim().replace(/\*+\/?\s*$/, '').trim() ?? '';
}

/** Set the version everywhere: main-file header, readme Stable tag, every *_VERSION constant. */
export async function setVersion(pluginDir: string, from: string, to: string): Promise<void> {
  for (const f of await findPhpFiles(pluginDir)) {
    let src = await readFile(f, 'utf8');
    const before = src;
    src = src.replace(/^([ \t/*#@]*Version\s*:\s*)(.+)$/im, (_m, p1: string, v: string) => (v.trim() === from ? p1 + to : _m));
    src = src.replace(/(define\s*\(\s*['"][A-Z0-9_]*_VERSION['"]\s*,\s*['"])([^'"]+)(['"]\s*\))/g, (_m, a: string, v: string, b: string) => (v === from ? a + to + b : _m));
    src = src.replace(/(const\s+[A-Z0-9_]*_VERSION\s*=\s*['"])([^'"]+)(['"])/g, (_m, a: string, v: string, b: string) => (v === from ? a + to + b : _m));
    if (src !== before) await writeFile(f, src, 'utf8');
  }
  const readmePath = join(pluginDir, 'readme.txt');
  const readme = await readFile(readmePath, 'utf8').catch(() => '');
  if (readme) await writeFile(readmePath, readme.replace(/^(\s*Stable tag\s*:\s*)(.+)$/im, `$1${to}`), 'utf8');
}

const STOP = new Set(['the', 'and', 'for', 'with', 'your', 'a', 'an', 'of', 'to', 'wp', 'wordpress', 'plugin', 'by', 'in', 'on']);

/** Up to 5 directory tags from the plugin name + the features it registers. */
export function deriveTags(name: string, provides: string): string[] {
  const tags: string[] = [];
  for (const w of name.toLowerCase().split(/[^a-z0-9]+/)) if (w.length > 2 && !STOP.has(w)) tags.push(w);
  const p = provides.toLowerCase();
  if (p.includes('shortcode')) tags.push('shortcode');
  if (p.includes('rest')) tags.push('rest-api');
  if (p.includes('custom post type')) tags.push('custom-post-type');
  if (p.includes('widget')) tags.push('widget');
  if (p.includes('block')) tags.push('block');
  return [...new Set(tags)].slice(0, 5);
}

function section(readme: string, title: string): string | null {
  const re = new RegExp(`^==\\s*${title}\\s*==\\s*$([\\s\\S]*?)(?=^==\\s*[^=]|(?![\\s\\S]))`, 'im');
  const m = readme.match(re);
  return m ? m[1].trim() : null;
}

function setSection(readme: string, title: string, body: string): string {
  const re = new RegExp(`(^==\\s*${title}\\s*==\\s*$)([\\s\\S]*?)(?=^==\\s*[^=]|(?![\\s\\S]))`, 'im');
  if (re.test(readme)) return readme.replace(re, `$1\n\n${body.trim()}\n\n`);
  // Insert before Changelog when possible, else append.
  const cl = readme.search(/^==\s*Changelog\s*==\s*$/im);
  const block = `== ${title} ==\n\n${body.trim()}\n\n`;
  return cl >= 0 ? readme.slice(0, cl) + block + readme.slice(cl) : readme.trimEnd() + '\n\n' + block;
}

function setHeaderLine(readme: string, field: string, value: string): string {
  const re = new RegExp(`^${field}\\s*:.*$`, 'im');
  if (re.test(readme)) return readme.replace(re, `${field}: ${value}`);
  // Add after the "=== Name ===" line + existing header fields block.
  const lines = readme.split('\n');
  let i = 1;
  while (i < lines.length && /^[A-Za-z ]+:\s*/.test(lines[i])) i++;
  lines.splice(i, 0, `${field}: ${value}`);
  return lines.join('\n');
}

/**
 * Bring readme.txt to directory standard without changing what the plugin says about itself:
 * header fields (Contributors, Tags, Tested up to, Requires, Stable tag, License), a short description of
 * at most 150 characters, and the Description / Installation / FAQ / Screenshots / Changelog sections.
 */
export async function prepareReadme(
  pluginDir: string,
  o: {
    name: string;
    username: string;
    testedUpTo: string;
    version: string;
    requiresWp: string;
    requiresPhp: string;
    provides: string;
    screenshots: string[];
    /** Where the plugin's screens live, e.g. "Settings → Heatmap Tracker". */
    locations?: string[];
    changelogLine: string;
  },
): Promise<void> {
  const path = join(pluginDir, 'readme.txt');
  let r = await readFile(path, 'utf8').catch(() => '');
  if (!r.trim()) r = `=== ${o.name} ===\n`;
  if (!/^===\s*.+\s*===\s*$/m.test(r.split('\n')[0])) r = `=== ${o.name} ===\n` + r;

  r = setHeaderLine(r, 'Contributors', o.username);
  const existingTags = headerField(r, 'Tags');
  const tags = existingTags
    ? existingTags.split(',').map((t) => t.trim().toLowerCase()).filter(Boolean).slice(0, 5)
    : deriveTags(o.name, o.provides);
  if (tags.length) r = setHeaderLine(r, 'Tags', tags.join(', '));
  r = setHeaderLine(r, 'Requires at least', headerField(r, 'Requires at least') || o.requiresWp);
  r = setHeaderLine(r, 'Tested up to', o.testedUpTo);
  r = setHeaderLine(r, 'Requires PHP', headerField(r, 'Requires PHP') || o.requiresPhp);
  r = setHeaderLine(r, 'Stable tag', o.version);
  r = setHeaderLine(r, 'License', 'GPLv2 or later');
  r = setHeaderLine(r, 'License URI', 'https://www.gnu.org/licenses/gpl-2.0.html');

  // Short description: first non-header, non-section line after the header block; max 150 characters.
  const lines = r.split('\n');
  let i = 1;
  while (i < lines.length && /^[A-Za-z ]+:\s*/.test(lines[i])) i++;
  while (i < lines.length && lines[i].trim() === '') i++;
  const desc = section(r, 'Description') ?? '';
  if (i >= lines.length || lines[i].startsWith('==')) {
    const short = (desc.split('\n')[0] || o.name).slice(0, 150);
    lines.splice(i, 0, short, '');
  } else if (lines[i].length > 150) {
    const cut = lines[i].slice(0, 147);
    lines[i] = cut.slice(0, Math.max(cut.lastIndexOf(' '), 100)).trimEnd() + '…';
  }
  r = lines.join('\n');

  if (!section(r, 'Description')) r = setSection(r, 'Description', lines[i] || o.name);
  // A one-line description reads as a stub to reviewers (guideline 12): add a Features list built from what the
  // plugin actually registers and where it lives.
  const descNow = section(r, 'Description') ?? '';
  if (descNow.split(/\s+/).filter(Boolean).length < 40 && !/^=\s*Features\s*=/m.test(descNow)) {
    const feats = [
      ...(o.provides ? o.provides.split(' · ').map((f) => f.trim()).filter((f) => f && !/^No admin menu/i.test(f)) : []),
      ...(o.locations ?? []).map((l) => `Where to find it: ${l}.`),
    ];
    r = setSection(r, 'Description', `${descNow}\n\n= Features =\n\n${[...new Set(feats)].map((f) => `* ${f}`).join('\n')}`);
  }
  if (!section(r, 'Installation')) {
    r = setSection(r, 'Installation', '1. Upload the plugin folder to `/wp-content/plugins/`, or install it from Plugins → Add New.\n2. Activate it through the Plugins screen.');
  }
  const faq = section(r, 'Frequently Asked Questions');
  if (!faq || /^= Where do I find it after activating\? =/m.test(faq)) {
    const where = o.locations?.length
      ? 'After activating, go to ' + o.locations.join(', or ') + '.'
      : 'After activating, look for its screen in the WordPress admin menu.';
    r = setSection(r, 'Frequently Asked Questions', `= Where do I find it after activating? =\n\n${where}`);
  }
  if (o.screenshots.length) {
    r = setSection(r, 'Screenshots', o.screenshots.map((c, n) => `${n + 1}. ${c}`).join('\n'));
  }
  // Changelog: add an entry for this version if missing.
  const cl = section(r, 'Changelog') ?? '';
  if (!new RegExp(`^=\\s*${o.version.replace(/\./g, '\\.')}\\s*=`, 'm').test(cl)) {
    r = setSection(r, 'Changelog', `= ${o.version} =\n* ${o.changelogLine}\n\n${cl}`);
  }
  await writeFile(path, r.replace(/\n{3,}/g, '\n\n').trimEnd() + '\n', 'utf8');
}

/** Readme problems the directory would reject or the review would flag. Empty = OK. */
export async function readmeIssues(pluginDir: string, o: { version: string; username: string; testedUpTo: string }): Promise<string[]> {
  const r = await readFile(join(pluginDir, 'readme.txt'), 'utf8').catch(() => '');
  const issues: string[] = [];
  if (!r) return ['readme.txt is missing.'];
  if (!/^===\s*.+\s*===\s*$/m.test(r.split('\n')[0])) issues.push('readme.txt must start with "=== Plugin Name ===".');
  const contributors = headerField(r, 'Contributors').split(',').map((s) => s.trim().toLowerCase());
  if (!contributors.includes(o.username.toLowerCase())) issues.push(`Contributors must include your WordPress.org username "${o.username}".`);
  const tags = headerField(r, 'Tags').split(',').map((s) => s.trim()).filter(Boolean);
  if (tags.length > 5) issues.push(`At most 5 tags are allowed (found ${tags.length}).`);
  if (headerField(r, 'Tested up to') !== o.testedUpTo) issues.push(`"Tested up to" should be ${o.testedUpTo} (the current WordPress version).`);
  if (headerField(r, 'Stable tag') !== o.version) issues.push(`"Stable tag" must be ${o.version}.`);
  if (!/gpl/i.test(headerField(r, 'License'))) issues.push('License must be GPL-compatible (GPLv2 or later).');
  for (const s of ['Description', 'Installation', 'Changelog']) if (!section(r, s)) issues.push(`Missing the "== ${s} ==" section.`);
  const lines = r.split('\n');
  let i = 1;
  while (i < lines.length && /^[A-Za-z ]+:\s*/.test(lines[i])) i++;
  while (i < lines.length && lines[i].trim() === '') i++;
  const short = lines[i] ?? '';
  if (!short || short.startsWith('==')) issues.push('Missing the short description line under the header.');
  else if (short.length > 150) issues.push(`Short description is ${short.length} characters; the limit is 150.`);
  return issues;
}

/** Name/slug words the directory refuses outright (Plugin Check reports most of these too). */
export function nameIssues(name: string, slug: string): string[] {
  const out: string[] = [];
  if (/\bplugin\b/i.test(name) || /(^|-)plugin(-|$)/.test(slug)) out.push('The name or slug contains "plugin", which the directory does not allow.');
  if (/\bwordpress\b/i.test(name) || /wordpress/.test(slug)) out.push('The name or slug contains "WordPress", a trademark the directory does not allow.');
  if (/^wp-/.test(slug) || /^wp\s/i.test(name)) out.push('Names starting with "WP" are not allowed (WordPress trademark).');
  return out;
}

interface PcRow {
  type?: string;
  code?: string;
  message?: string;
  file?: string;
  line?: number;
}

function parsePcRows(stdout: string): PcRow[] {
  const rows: PcRow[] = [];
  let file = '';
  for (const raw of stdout.split('\n')) {
    const line = raw.trim();
    const fm = line.match(/^FILE:\s*(.+)$/);
    if (fm) {
      file = fm[1].trim();
      continue;
    }
    if (line.startsWith('[') && line.endsWith(']')) {
      try {
        for (const o of JSON.parse(line) as PcRow[]) rows.push({ ...o, file: o.file ?? file });
      } catch {
        /* not a findings array */
      }
    }
  }
  return rows;
}

/**
 * Plugin Check with every category, the way the directory review sees it. Any ERROR fails, and so do the
 * trademark warnings (the review rejects those names).
 */
export async function strictPluginCheck(env: WpEnv, slug: string): Promise<{ errors: string[]; warnings: string[] }> {
  await env.wp(['plugin', 'activate', 'plugin-check']);
  const res = await env.wp([
    'plugin',
    'check',
    slug,
    '--format=json',
    '--exclude-directories=tests,vendor,node_modules',
    '--exclude-files=phpunit.xml.dist,phpunit.xml,.distignore,SPEC.json,.phpunit.result.cache',
  ]);
  const errors: string[] = [];
  const warnings: string[] = [];
  for (const r of parsePcRows(res.stdout)) {
    const text = `${r.file ?? slug}:${r.line ?? '?'} (${r.code ?? '?'}) ${r.message ?? ''}`.trim();
    if ((r.type ?? '').toUpperCase() === 'ERROR' || /trademark/i.test(r.code ?? '')) errors.push(text);
    else warnings.push(text);
  }
  return { errors, warnings };
}

/** Admin screens the plugin adds (diff of the admin menu with the plugin on vs off) + its shortcodes. */
export async function discoverScreens(
  env: WpEnv,
  slug: string,
  pluginDir: string,
): Promise<Array<{ path: string; caption: string; admin: boolean; location: string }>> {
  const menuDump = `
    wp_set_current_user( 1 );
    require_once ABSPATH . 'wp-admin/includes/admin.php';
    global $menu, $submenu;
    $menu = array(); $submenu = array();
    do_action( '_admin_menu' ); do_action( 'admin_menu' );
    $out = array();
    foreach ( (array) $menu as $m ) { if ( ! empty( $m[2] ) ) { $out[] = array( '', $m[2], wp_strip_all_tags( (string) $m[0] ), wp_strip_all_tags( (string) ( $m[3] ?? $m[0] ) ) ); } }
    foreach ( (array) $submenu as $parent => $items ) { foreach ( $items as $m ) { $out[] = array( $parent, $m[2], wp_strip_all_tags( (string) $m[0] ), wp_strip_all_tags( (string) ( $m[3] ?? $m[0] ) ) ); } }
    echo wp_json_encode( $out );`;
  const read = async (): Promise<string[][]> => {
    const r = await env.wp(['eval', menuDump]);
    const line = r.stdout.split('\n').reverse().find((l) => l.trim().startsWith('['));
    try {
      return line ? (JSON.parse(line) as string[][]) : [];
    } catch {
      return [];
    }
  };
  const withPlugin = await read();
  await env.wp(['plugin', 'deactivate', slug]);
  const without = await read();
  await env.wp(['plugin', 'activate', slug]);
  const key = (e: string[]) => `${e[0]}|${e[1]}`;
  const base = new Set(without.map(key));
  // Human menu path ("Settings → Heatmap Tracker") from core parents or the plugin's own top-level menu.
  const CORE: Record<string, string> = {
    'index.php': 'Dashboard', 'edit.php': 'Posts', 'upload.php': 'Media', 'edit.php?post_type=page': 'Pages',
    'edit-comments.php': 'Comments', 'themes.php': 'Appearance', 'plugins.php': 'Plugins', 'users.php': 'Users',
    'tools.php': 'Tools', 'options-general.php': 'Settings',
  };
  const topTitle = (slug: string) => withPlugin.find((e) => !e[0] && e[1] === slug)?.[2]?.replace(/\s+\d+$/, '').trim();
  const shots: Array<{ path: string; caption: string; admin: boolean; location: string }> = [];
  const seen = new Set<string>();
  for (const [parent, page, menuTitle, pageTitle] of withPlugin.filter((e) => !base.has(key(e)))) {
    let path: string;
    if (page.includes('.php')) path = `/wp-admin/${page}`;
    else if (!parent || !parent.includes('.php')) path = `/wp-admin/admin.php?page=${encodeURIComponent(page)}`;
    else path = `/wp-admin/${parent}${parent.includes('?') ? '&' : '?'}page=${encodeURIComponent(page)}`;
    if (seen.has(path)) continue;
    seen.add(path);
    const title = (menuTitle || pageTitle || 'Admin screen').replace(/\s+\d+$/, '').trim();
    const parentLabel = parent ? CORE[parent] ?? topTitle(parent) ?? '' : '';
    const location = parentLabel && parentLabel !== title ? `${parentLabel} → ${title}` : title;
    const what = (pageTitle || title).replace(/\s+\d+$/, '').trim();
    shots.push({ path, caption: `${what} screen (${location})`, admin: true, location });
  }
  // Front end: one page showing each shortcode the plugin registers.
  let code = '';
  for (const f of await findPhpFiles(pluginDir)) code += await readFile(f, 'utf8').catch(() => '');
  const tags = [...new Set([...code.matchAll(/add_shortcode\(\s*['"]([a-z0-9_-]+)['"]/gi)].map((m) => m[1]))].slice(0, 2);
  if (tags.length) {
    const r = await env.wp(['post', 'create', '--post_type=page', '--post_status=publish', `--post_title=${tags.map((t) => `[${t}]`).join(' ')} demo`, `--post_content=${tags.map((t) => `[${t}]`).join('\n\n')}`, '--porcelain']);
    const id = r.stdout.trim().split('\n').pop()?.trim();
    if (id && /^\d+$/.test(id)) {
      const sc = tags.map((t) => `[${t}]`).join(' and ');
      shots.push({ path: `/?page_id=${id}`, caption: `The ${sc} shortcode on a page`, admin: true, location: `the ${sc} shortcode, placed in any page or post` });
    }
  }
  if (!shots.length) {
    shots.push({ path: '/wp-admin/plugins.php?plugin_status=active', caption: 'The plugin active on the Plugins screen', admin: true, location: 'Plugins → Installed Plugins' });
  }
  return shots.slice(0, 4);
}

export interface GuidelineRow {
  n: string;
  title: string;
  /** pass = checked and fine; fail = blocks submission; review = a person must judge it; na = does not apply */
  status: 'pass' | 'fail' | 'review' | 'na';
  note: string;
}

/** Brands the directory will not accept as the start of a slug/name (guideline 17). */
const TRADEMARKS = ['wordpress', 'wp', 'woocommerce', 'woo', 'gutenberg', 'jetpack', 'google', 'gmail', 'youtube', 'facebook', 'instagram', 'whatsapp', 'meta', 'twitter', 'tiktok', 'amazon', 'paypal', 'stripe', 'apple', 'microsoft', 'openai', 'chatgpt', 'anthropic', 'claude', 'elementor', 'yoast', 'akismet', 'mailchimp', 'hubspot', 'shopify', 'zoom', 'slack'];

/** Libraries WordPress ships; plugins must use those, not bundle their own (guideline 13). */
const BUNDLED_LIB = /(^|\/)(jquery(\.min)?(-[\d.]+)?\.js|jquery-ui[\w.-]*\.js|class-phpmailer\.php|PHPMailer\.php|simplepie[\w.-]*\.(php|inc)|class-phpass\.php|PasswordHash\.php|backbone(\.min)?\.js|underscore(\.min)?\.js|moxie(\.min)?\.js|plupload[\w.-]*\.js|react(-dom)?(\.production)?(\.min)?\.js|lodash(\.min)?\.js|moment(\.min)?\.js)$/i;

async function walkFiles(dir: string, rel = ''): Promise<string[]> {
  const { readdir } = await import('node:fs/promises');
  const out: string[] = [];
  for (const e of await readdir(join(dir, rel), { withFileTypes: true })) {
    if (['.git', 'node_modules', 'vendor', 'tests'].includes(e.name)) continue;
    const r = rel ? `${rel}/${e.name}` : e.name;
    if (e.isDirectory()) out.push(...(await walkFiles(dir, r)));
    else out.push(r);
  }
  return out;
}

/**
 * Check a plugin against the WordPress.org Detailed Plugin Guidelines as far as code can.
 * `fail` rows block submission; `review` rows are listed for a person to judge.
 */
export async function guidelineChecks(pluginDir: string, o: { name: string; slug: string }): Promise<GuidelineRow[]> {
  const files = await walkFiles(pluginDir);
  const code: Record<string, string> = {};
  for (const f of files) if (/\.(php|js|css|html?)$/i.test(f)) code[f] = await readFile(join(pluginDir, f), 'utf8').catch(() => '');
  const all = Object.values(code).join('\n');
  const php = Object.entries(code).filter(([f]) => f.endsWith('.php')).map(([, s]) => s).join('\n');
  const readme = await readFile(join(pluginDir, 'readme.txt'), 'utf8').catch(() => '');
  const main = (await mainFile(pluginDir)) ?? '';
  const header = main ? await readFile(main, 'utf8') : '';
  const rows: GuidelineRow[] = [];
  const add = (n: string, title: string, status: GuidelineRow['status'], note: string) => rows.push({ n, title, status, note });
  const hits = (re: RegExp) => Object.entries(code).filter(([, s]) => re.test(s)).map(([f]) => f);

  // 1. GPL
  const lic = headerField(header, 'License') + ' ' + headerField(readme, 'License');
  add('1', 'GPL-compatible licence', /gpl/i.test(lic) ? 'pass' : 'fail', /gpl/i.test(lic) ? 'GPL licence in the header and readme.' : 'Header/readme licence must be GPLv2 or later.');

  // 2. Third-party terms — flagged with 7/8 below.
  add('2', 'Developer responsible for contents and third-party terms', 'review', 'Confirm the licence of any images or code you added by hand.');

  // 3. Stable version only from the directory: no custom updater.
  const updater = hits(/pre_set_site_transient_update_plugins|site_transient_update_plugins|plugins_api\s*['"]?\s*,|['"]update_plugins_/);
  add('3', 'Updates only from WordPress.org', updater.length ? 'fail' : 'pass', updater.length ? `Custom update code in ${updater.join(', ')}.` : 'No custom update mechanism.');

  // 4. Human-readable code.
  const obfus: string[] = [];
  for (const [f, s] of Object.entries(code)) {
    if (/eval\s*\(\s*function\s*\(\s*p\s*,\s*a\s*,\s*c\s*,\s*k\s*,\s*e/.test(s) || /eval\s*\(\s*(base64_decode|gzinflate|str_rot13)\s*\(/.test(s)) obfus.push(`${f} (packed/encoded eval)`);
    else if (!/\.css$/i.test(f) && s.split('\n').some((l) => l.length > 2000)) obfus.push(`${f} (minified)`);
  }
  add('4', 'Human-readable code', obfus.length ? 'fail' : 'pass', obfus.length ? `Not readable: ${obfus.join(', ')} — ship the unminified source or link to it in the readme.` : 'No packed, encoded or minified-only code.');

  // 5. Trialware.
  const trial = hits(/\b(free trial|trial (period|expired)|upgrade to (pro|premium)|license key|licence key|unlock (pro|premium))\b/i);
  add('5', 'No trialware', trial.length ? 'review' : 'pass', trial.length ? `Upsell/licence wording in ${trial.join(', ')} — make sure nothing in the plugin is locked.` : 'Nothing locked or time-limited.');

  // 6/7/8. External services, data collection, remote code.
  const external = hits(/wp_remote_(get|post|request|head)\s*\(|wp_safe_remote_|curl_init\s*\(|file_get_contents\s*\(\s*['"]https?:|fetch\s*\(\s*['"]https?:\/\/|new\s+WebSocket\s*\(/);
  const hasExtSection = /^==\s*External services\s*==/im.test(readme);
  add('6', 'Software as a Service documented', external.length ? (hasExtSection ? 'review' : 'fail') : 'na',
    external.length ? (hasExtSection ? 'Calls external services; readme has "External services" — check it names each service, what is sent, when, and links its terms/privacy.' : `Calls external services (${external.join(', ')}) but the readme has no "== External services ==" section naming each service, what data is sent and when, with links to its terms and privacy policy.`) : 'No external services.');
  const collects = /wp_ajax_nopriv_|['"]permission_callback['"]\s*=>\s*['"]__return_true['"]|REMOTE_ADDR|setcookie\s*\(|document\.cookie|localStorage|sessionStorage|navigator\.sendBeacon/.test(all) || publicWriteRoute(php);
  const privacyDoc = /^=+\s*Privacy\s*=+/im.test(readme) || /privacy/i.test(section(readme, 'Description') ?? '');
  const policyText = /wp_add_privacy_policy_content\s*\(/.test(php);
  if (external.length && !hasExtSection) add('7', 'No tracking without consent; data use documented', 'fail', 'Contacts external servers without readme documentation.');
  else if (collects && (!privacyDoc || !policyText)) {
    add('7', 'No tracking without consent; data use documented', 'fail',
      `Collects visitor data (public endpoint, IP, cookies or storage) but ${!privacyDoc ? 'the readme does not explain what is collected and why' : ''}${!privacyDoc && !policyText ? ', and ' : ''}${!policyText ? 'no privacy-policy text is suggested (wp_add_privacy_policy_content)' : ''}.`);
  } else add('7', 'No tracking without consent; data use documented', collects ? 'pass' : 'na', collects ? 'Visitor data documented in the readme and suggested for the privacy policy; no external contact.' : 'Collects no visitor data and contacts no external server.');
  const cdn = hits(/(src|href)\s*=\s*['"]https?:\/\/(?!fonts\.googleapis\.com|fonts\.gstatic\.com)[^'"]+\.(js|css)|wp_enqueue_(script|style)\s*\([^)]*['"]https?:\/\/(?!fonts\.googleapis)/i);
  const iframe = hits(/<iframe/i);
  add('8', 'No executable code from third parties', cdn.length ? 'fail' : iframe.length ? 'review' : 'pass',
    cdn.length ? `Loads JS/CSS from another server in ${cdn.join(', ')} — include it locally.` : iframe.length ? `Uses iframes in ${iframe.join(', ')} — not allowed for admin pages.` : 'All scripts and styles are local; no iframes.');

  // 9. Dishonest claims, e.g. guaranteed legal compliance.
  const claims = readme.match(/\b(GDPR|CCPA|HIPAA|ADA|WCAG|PCI)[- ]?(compliant|compliance guaranteed|certified)|\bguarantee[sd]? (legal )?compliance\b|\bmakes? (your site|you) (legally )?compliant\b/gi);
  add('9', 'Nothing illegal, dishonest or offensive', claims ? 'fail' : 'review', claims ? `The readme claims legal compliance (${[...new Set(claims)].join(', ')}), which the directory forbids.` : 'No compliance claims found; the rest is a human judgement.');

  // 10. Credits / external links on the public site.
  const credits = hits(/powered by|designed by|developed by/i).filter((f) => !/readme/i.test(f));
  add('10', 'No public credits or links without opt-in', credits.length ? 'review' : 'pass', credits.length ? `Credit text in ${credits.join(', ')} — must be off by default and opt-in.` : 'No credits or links added to the public site.');

  // 11. Dashboard hijacking: global admin notices that cannot be dismissed.
  const noticeFiles = hits(/add_action\s*\(\s*['"](admin_notices|all_admin_notices|network_admin_notices)['"]/);
  const undismissible = noticeFiles.filter((f) => !/is-dismissible/.test(code[f]));
  add('11', 'Does not hijack the dashboard', undismissible.length ? 'review' : 'pass', undismissible.length ? `Site-wide notices without dismiss in ${undismissible.join(', ')} — limit them to the plugin's page or make them dismissible.` : noticeFiles.length ? 'Site-wide notices are dismissible.' : 'No site-wide notices.');

  // 12. Readme not spam.
  const tags = headerField(readme, 'Tags').split(',').map((t) => t.trim().toLowerCase()).filter(Boolean);
  const descWords = (section(readme, 'Description') ?? '').split(/\s+/).filter(Boolean).length;
  const spam = tags.length > 5 ? `${tags.length} tags (max 5)` : descWords < 40 ? `the description is only ${descWords} words — explain what it does for a person reading it` : '';
  add('12', 'Readme written for people, not spam', spam ? 'fail' : 'pass', spam ? `Fix: ${spam}.` : `${tags.length} tag(s); ${descWords}-word description.`);

  // 13. Default libraries.
  const libs = files.filter((f) => BUNDLED_LIB.test(f));
  add('13', 'Uses WordPress\' bundled libraries', libs.length ? 'fail' : 'pass', libs.length ? `Bundles libraries WordPress already ships: ${libs.join(', ')} — enqueue the core copies instead.` : 'No bundled copies of core libraries.');

  // 14/15/16.
  add('14', 'No frequent trivial SVN commits', 'na', 'Release practice; the push makes one commit per version.');
  add('15', 'Version incremented for each release', 'pass', 'Prepare and Push always use a new, higher version.');
  add('16', 'Complete plugin at submission', 'review', 'Confirm the plugin does what its description says (the spec-coverage check covers registered features).');

  // 17. Trademarks at the start of the slug or name.
  const first = o.slug.split('-')[0].toLowerCase();
  const nameFirst = o.name.toLowerCase().split(/[^a-z0-9]+/).filter(Boolean)[0] ?? '';
  const tm = TRADEMARKS.find((t) => t === first || t === nameFirst);
  add('17', 'Respects trademarks and project names', tm ? 'fail' : 'review', tm ? `The slug/name starts with "${tm}", a brand you don't own — use "<Your name> for ${tm}" instead.` : 'No known brand at the start of the name; check the name is not someone else\'s product.');
  add('18', 'Directory may update guidelines / remove plugins', 'na', 'Nothing to check.');
  return rows;
}

/**
 * True when a REST route accepts writes from anyone: its permission callback is a named function whose body
 * never checks current_user_can (e.g. a public event collector).
 */
function publicWriteRoute(php: string): boolean {
  for (const m of php.matchAll(/register_rest_route\s*\(([\s\S]*?)\)\s*;/g)) {
    const route = m[1];
    if (!/['"]methods['"]\s*=>\s*(['"](POST|PUT|PATCH|DELETE)['"]|WP_REST_Server::(CREATABLE|EDITABLE|DELETABLE))/i.test(route)) continue;
    const cb = route.match(/['"]permission_callback['"]\s*=>\s*['"]([A-Za-z0-9_]+)['"]/)?.[1];
    if (!cb) continue;
    if (cb === '__return_true') return true;
    const body = php.match(new RegExp(`function\\s+${cb}\\s*\\([^)]*\\)[^{]*\\{([\\s\\S]*?)\\n\\}`))?.[1] ?? '';
    if (body && !/current_user_can\s*\(/.test(body)) return true;
  }
  return false;
}

/** Markdown table for the report. */
export function guidelineTable(rows: GuidelineRow[]): string {
  const icon = { pass: '✅', fail: '❌', review: '👀', na: '—' } as const;
  return ['| # | Guideline | Result | Detail |', '|---|---|---|---|', ...rows.map((r) => `| ${r.n} | ${r.title} | ${icon[r.status]} ${r.status} | ${r.note.replace(/\|/g, '\\|')} |`)].join('\n');
}
