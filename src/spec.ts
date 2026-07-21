/**
 * Structured plugin spec: the contract the spec-writer produces (as build/<slug>/SPEC.json)
 * and the coder implements. Also owns template rendering + scaffolding.
 */
import { readFile, writeFile, mkdir, copyFile } from 'node:fs/promises';
import { join, dirname } from 'node:path';

export interface SpecField {
  key: string;
  label: string;
  type: 'text' | 'textarea' | 'int' | 'email' | 'url' | 'checkbox' | 'select' | 'date' | 'color' | 'number';
  options?: string[];
}

export interface SpecTaxonomy {
  key: string;
  labelSingular: string;
  labelPlural: string;
  postTypes: string[];
  hierarchical: boolean;
  public: boolean;
}

export interface SpecAjaxAction {
  action: string;
  /** true if the endpoint is available to logged-out users (wp_ajax_nopriv). */
  public: boolean;
  capability?: string;
  description: string;
}

export interface SpecCronEvent {
  hook: string;
  recurrence: 'hourly' | 'twicedaily' | 'daily' | 'weekly';
  description: string;
}

export interface SpecWidget {
  idBase: string;
  name: string;
  description: string;
}

export interface SpecPostType {
  key: string;
  labelSingular: string;
  labelPlural: string;
  public: boolean;
  supports: string[];
  fields?: SpecField[];
  adminColumns?: string[];
}

export interface SpecAdminPage {
  type: 'settings' | 'menu' | 'submenu';
  title: string;
  menuSlug: string;
  capability: string;
  fields?: SpecField[];
}

export interface SpecShortcode {
  tag: string;
  description: string;
  attributes?: Array<{ name: string; default: string; description?: string }>;
}

export interface SpecBlock {
  name: string;
  title: string;
  description: string;
}

export interface SpecRestEndpoint {
  namespace: string;
  route: string;
  methods: string[];
  public: boolean;
  capability?: string;
  description: string;
}

export interface StructuredSpec {
  slug: string;
  pluginName: string;
  description: string;
  version: string;
  prefix: string;
  requiresWp: string;
  requiresPhp: string;
  capabilities: string[];
  postTypes: SpecPostType[];
  taxonomies: SpecTaxonomy[];
  adminPages: SpecAdminPage[];
  shortcodes: SpecShortcode[];
  blocks: SpecBlock[];
  restEndpoints: SpecRestEndpoint[];
  ajaxActions: SpecAjaxAction[];
  cronEvents: SpecCronEvent[];
  widgets: SpecWidget[];
  dataStorage: string;
  securityRequirements: string[];
  /** PHP boolean expressions the smoke test should assert are true, e.g. "post_type_exists('x_item')". */
  smokeAssertions: string[];
}

const RESERVED_PREFIXES = ['wp', 'wp_', '__', '_'];

const ARRAY_KEYS = [
  'capabilities',
  'postTypes',
  'taxonomies',
  'adminPages',
  'shortcodes',
  'blocks',
  'restEndpoints',
  'ajaxActions',
  'cronEvents',
  'widgets',
  'securityRequirements',
  'smokeAssertions',
] as const;

/** Fill any missing array fields with [] so older specs and partial specs stay valid. */
export function normalizeSpec(spec: Record<string, unknown>): Record<string, unknown> {
  for (const k of ARRAY_KEYS) {
    if (!Array.isArray(spec[k])) spec[k] = [];
  }
  return spec;
}

/** Validate a parsed spec object; returns a list of problems (empty = valid). */
export function validateSpec(spec: unknown): string[] {
  const problems: string[] = [];
  const s = spec as Partial<StructuredSpec>;
  const need = (cond: boolean, msg: string) => {
    if (!cond) problems.push(msg);
  };

  need(typeof s.slug === 'string' && /^[a-z][a-z0-9-]{2,}$/.test(s.slug ?? ''), 'slug must be kebab-case (a-z0-9-), >=3 chars.');
  need(typeof s.pluginName === 'string' && !!s.pluginName, 'pluginName is required.');
  need(typeof s.description === 'string' && !!s.description, 'description is required.');
  need(typeof s.version === 'string' && /^\d+\.\d+(\.\d+)?$/.test(s.version ?? ''), 'version must look like 1.0.0.');
  need(
    typeof s.prefix === 'string' && /^[a-z][a-z0-9]{2,7}$/.test(s.prefix ?? '') && !RESERVED_PREFIXES.includes(s.prefix ?? ''),
    'prefix must be 3-8 lowercase alnum chars and not a reserved WP prefix (wp/__/_).',
  );
  need(typeof s.requiresWp === 'string' && !!s.requiresWp, 'requiresWp is required.');
  need(typeof s.requiresPhp === 'string' && !!s.requiresPhp, 'requiresPhp is required.');
  for (const arrKey of ARRAY_KEYS) {
    need(Array.isArray((s as Record<string, unknown>)[arrKey]), `${arrKey} must be an array (use [] if none).`);
  }
  return problems;
}

/** Placeholder map for template rendering. */
export function placeholders(spec: StructuredSpec): Record<string, string> {
  const cls = spec.prefix.charAt(0).toUpperCase() + spec.prefix.slice(1);
  return {
    SLUG: spec.slug,
    PLUGIN_NAME: spec.pluginName,
    DESCRIPTION: spec.description,
    VERSION: spec.version,
    REQUIRES_WP: spec.requiresWp,
    REQUIRES_PHP: spec.requiresPhp,
    PREFIX: spec.prefix,
    UPREFIX: spec.prefix.toUpperCase(),
    CLASS: cls,
  };
}

function render(template: string, vars: Record<string, string>): string {
  return template.replace(/\{\{([A-Z_]+)\}\}/g, (_m, key) => vars[key] ?? `{{${key}}}`);
}

async function renderTo(templatePath: string, destPath: string, vars: Record<string, string>): Promise<void> {
  const tmpl = await readFile(templatePath, 'utf8');
  await mkdir(dirname(destPath), { recursive: true });
  await writeFile(destPath, render(tmpl, vars), 'utf8');
}

/**
 * Scaffold a fresh plugin workspace from templates into build/<slug>/, then write SPEC.json.
 * Returns the absolute plugin directory.
 */
export async function scaffoldPlugin(spec: StructuredSpec, repoRoot: string): Promise<string> {
  const vars = placeholders(spec);
  const tpl = join(repoRoot, 'harness', 'templates', 'plugin');
  const dir = join(repoRoot, 'build', spec.slug);
  await mkdir(join(dir, 'includes'), { recursive: true });
  await mkdir(join(dir, 'tests'), { recursive: true });

  await renderTo(join(tpl, 'main.php.tmpl'), join(dir, `${spec.slug}.php`), vars);
  await renderTo(join(tpl, 'uninstall.php.tmpl'), join(dir, 'uninstall.php'), vars);
  await renderTo(join(tpl, 'readme.txt.tmpl'), join(dir, 'readme.txt'), vars);
  await renderTo(join(tpl, 'tests', 'bootstrap.php.tmpl'), join(dir, 'tests', 'bootstrap.php'), vars);
  await renderTo(join(tpl, 'tests', 'test-smoke.php.tmpl'), join(dir, 'tests', 'test-smoke.php'), vars);
  await copyFile(join(tpl, 'phpunit.xml.dist'), join(dir, 'phpunit.xml.dist'));
  await copyFile(join(tpl, '.distignore'), join(dir, '.distignore'));

  await writeFile(join(dir, 'SPEC.json'), JSON.stringify(spec, null, 2) + '\n', 'utf8');
  return dir;
}

/** Read and parse a SPEC.json file (normalized so missing array fields default to []). */
export async function readSpec(path: string): Promise<StructuredSpec> {
  const raw = await readFile(path, 'utf8');
  return normalizeSpec(JSON.parse(raw)) as unknown as StructuredSpec;
}
