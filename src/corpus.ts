/**
 * Golden corpus + flywheel. Every harness-verified plugin is appended here as a reusable, gate-passing
 * exemplar. This corpus powers exemplar-RAG now (retrieve the closest working plugin as a template) and
 * becomes a finetuning dataset later — with the rare property that every label is objectively correct
 * (it passed all 8 gates). Stored under corpus/ (tracked); manifest at corpus/manifest.json.
 */
import { readFile, writeFile, mkdir, cp, readdir, access } from 'node:fs/promises';
import { join, relative } from 'node:path';
import type { StructuredSpec } from './spec.js';

export type FeatureTag =
  | 'cpt'
  | 'taxonomy'
  | 'metabox'
  | 'settings'
  | 'shortcode'
  | 'block'
  | 'rest'
  | 'ajax'
  | 'cron'
  | 'widget';

export interface CorpusEntry {
  slug: string;
  pluginName: string;
  description: string;
  version: string;
  prefix: string;
  tags: FeatureTag[];
  /** Source files (relative paths) stored under corpus/<slug>/. */
  files: string[];
}

interface Manifest {
  entries: CorpusEntry[];
}

const CORPUS_DIR = (repoRoot: string) => join(repoRoot, 'corpus');
const MANIFEST = (repoRoot: string) => join(CORPUS_DIR(repoRoot), 'manifest.json');

async function exists(p: string): Promise<boolean> {
  try {
    await access(p);
    return true;
  } catch {
    return false;
  }
}

/** Derive feature tags from a structured spec. */
export function featureTags(spec: StructuredSpec): FeatureTag[] {
  const tags = new Set<FeatureTag>();
  if (spec.postTypes.length) tags.add('cpt');
  if (spec.taxonomies?.length) tags.add('taxonomy');
  if (spec.postTypes.some((p) => p.fields?.length) || spec.adminPages.some((a) => a.type !== 'settings')) tags.add('metabox');
  if (spec.adminPages.some((a) => a.type === 'settings') || spec.adminPages.some((a) => a.fields?.length)) tags.add('settings');
  if (spec.shortcodes.length) tags.add('shortcode');
  if (spec.blocks.length) tags.add('block');
  if (spec.restEndpoints.length) tags.add('rest');
  if (spec.ajaxActions?.length) tags.add('ajax');
  if (spec.cronEvents?.length) tags.add('cron');
  if (spec.widgets?.length) tags.add('widget');
  return [...tags];
}

export async function readManifest(repoRoot: string): Promise<Manifest> {
  if (!(await exists(MANIFEST(repoRoot)))) return { entries: [] };
  try {
    return JSON.parse(await readFile(MANIFEST(repoRoot), 'utf8')) as Manifest;
  } catch {
    return { entries: [] };
  }
}

async function writeManifest(repoRoot: string, m: Manifest): Promise<void> {
  await mkdir(CORPUS_DIR(repoRoot), { recursive: true });
  await writeFile(MANIFEST(repoRoot), JSON.stringify(m, null, 2) + '\n', 'utf8');
}

/**
 * Files worth keeping as a template: PHP source (incl. tests/*.php as a passing-test pattern) + readme
 * + SPEC. Skip vendored deps, packaging config, zips, and caches.
 */
function keepInCorpus(rel: string): boolean {
  if (/(^|\/)(node_modules|vendor)\//.test(rel)) return false;
  if (/\.(zip|cache)$/.test(rel)) return false;
  if (/^(phpunit\.xml.*|\.distignore|BUILD-REPORT\.md|\.phpunit\.result\.cache)$/.test(rel)) return false;
  return /\.(php)$/.test(rel) || rel === 'readme.txt' || rel === 'SPEC.json';
}

/**
 * Append a verified plugin to the corpus. `spec` provides tags/metadata; `pluginDir` provides source.
 * Idempotent per slug (replaces an existing entry).
 */
export async function addToCorpus(repoRoot: string, pluginDir: string, spec: StructuredSpec): Promise<void> {
  const dest = join(CORPUS_DIR(repoRoot), spec.slug);
  // Copy only the files worth keeping, preserving relative paths.
  const kept = (await walk(pluginDir))
    .map((f) => relative(pluginDir, f).split('\\').join('/'))
    .filter(keepInCorpus)
    .sort();
  for (const rel of kept) {
    const to = join(dest, rel);
    await mkdir(join(to, '..'), { recursive: true });
    await cp(join(pluginDir, rel), to);
  }
  const files = kept;

  const entry: CorpusEntry = {
    slug: spec.slug,
    pluginName: spec.pluginName,
    description: spec.description,
    version: spec.version,
    prefix: spec.prefix,
    tags: featureTags(spec),
    files,
  };
  const m = await readManifest(repoRoot);
  m.entries = m.entries.filter((e) => e.slug !== spec.slug);
  m.entries.push(entry);
  m.entries.sort((a, b) => a.slug.localeCompare(b.slug));
  await writeManifest(repoRoot, m);
}

async function walk(dir: string): Promise<string[]> {
  const out: string[] = [];
  let entries;
  try {
    entries = await readdir(dir, { withFileTypes: true });
  } catch {
    return out;
  }
  for (const e of entries) {
    const p = join(dir, e.name);
    if (e.isDirectory()) out.push(...(await walk(p)));
    else out.push(p);
  }
  return out;
}

/** Read the full source of a corpus entry, concatenated for use as a template in a prompt. */
export async function readEntrySource(repoRoot: string, entry: CorpusEntry, maxChars = 12000): Promise<string> {
  const parts: string[] = [];
  let total = 0;
  // Prefer PHP over readme/SPEC when trimming.
  const ordered = [...entry.files].sort((a, b) => Number(b.endsWith('.php')) - Number(a.endsWith('.php')));
  for (const rel of ordered) {
    if (rel === 'SPEC.json') continue;
    const text = await readFile(join(CORPUS_DIR(repoRoot), entry.slug, rel), 'utf8').catch(() => '');
    if (!text) continue;
    if (total + text.length > maxChars) continue;
    total += text.length;
    parts.push(`----- ${entry.slug}/${rel} -----\n${text}`);
  }
  return parts.join('\n\n');
}

/** Seed the corpus from the committed examples/ (each is harness-verified). */
export async function seedCorpus(repoRoot: string, log: (m: string) => void = () => {}): Promise<number> {
  const examplesDir = join(repoRoot, 'examples');
  let dirs: string[] = [];
  try {
    dirs = (await readdir(examplesDir, { withFileTypes: true })).filter((d) => d.isDirectory()).map((d) => d.name);
  } catch {
    return 0;
  }
  let n = 0;
  for (const slug of dirs) {
    const dir = join(examplesDir, slug);
    const specPath = join(dir, 'SPEC.json');
    if (!(await exists(specPath))) continue;
    const spec = JSON.parse(await readFile(specPath, 'utf8')) as StructuredSpec;
    // Ensure new array fields exist.
    for (const k of ['taxonomies', 'ajaxActions', 'cronEvents', 'widgets'] as const) {
      if (!Array.isArray((spec as unknown as Record<string, unknown>)[k])) (spec as unknown as Record<string, unknown>)[k] = [];
    }
    await addToCorpus(repoRoot, dir, spec);
    log(`  + ${slug} [${featureTags(spec).join(', ')}]`);
    n++;
  }
  return n;
}
