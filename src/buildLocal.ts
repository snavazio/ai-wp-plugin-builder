/**
 * Phase 4 — FILE-PROTOCOL generator loop. Same pipeline as `build`, but instead of the Claude Agent SDK
 * the model returns whole files as text. The model is any chat platform from engines/llm.ts (default: a
 * local Ollama model; the builder service may hand it OpenAI-compatible or Gemini via AIWPB_LLM_* env).
 * A small local RAG (Ollama embeddings) feeds the WordPress rules to the model when it is available. The 8-gate harness remains the objective judge, so a
 * local model's output is held to the exact same bar — and the harness honestly reports where a
 * smaller model falls short.
 *
 *   npm run build-local -- specs/<name>.md
 *   OLLAMA_HOST=http://<olares-box>:11434 OLLAMA_MODEL=qwen2.5-coder:32b npm run build-local -- specs/x.md
 */
import { readFile, writeFile, mkdir } from 'node:fs/promises';
import { join, relative } from 'node:path';
import type { BuildEnv } from './build.js';
import { loadRules } from './agents.js';
import { scaffoldPlugin, validateSpec, normalizeSpec, summarizeProvides, type StructuredSpec } from './spec.js';
import { runPipeline } from './pipeline.js';
import { renderTerminal, renderMarkdown } from './report.js';
import { WpEnv, dockerAvailable } from './wpEnv.js';
import { exec, findPhpFiles } from './util/exec.js';
import { ollamaConfig } from './engines/ollama.js';
import { llmChat, platformFromEnv, testPlatform, type Platform, type ChatMessage } from './engines/llm.js';
import { loadOrBuildIndex, retrieve, formatContext, type RagIndex } from './rag.js';
import { parseFiles, extractJson, writeGeneratedFiles } from './fileProtocol.js';
import { emitResult } from './resultFile.js';
import { selectExemplars, formatExemplars } from './exemplarRag.js';
import { formatFixes } from './fixKb.js';
import { addToCorpus, readManifest, seedCorpus } from './corpus.js';
import type { PipelineResult } from './types.js';

const MAX_FIX_ITERATIONS = 6;

interface Usage {
  promptTokens: number;
  completionTokens: number;
  calls: number;
}

async function chat(llm: Platform, messages: ChatMessage[], usage: Usage, temperature = 0.1): Promise<string> {
  const r = await llmChat(llm, messages, { temperature, numCtx: 32768 });
  usage.promptTokens += r.promptTokens;
  usage.completionTokens += r.completionTokens;
  usage.calls += 1;
  return r.content;
}

/** Current plugin files rendered in the ===FILE=== protocol, so the model sees exact state. */
async function snapshotFiles(pluginDir: string): Promise<string> {
  const files = await findPhpFiles(pluginDir);
  // Include readme.txt too since version/stable-tag consistency matters.
  const parts: string[] = [];
  for (const f of files) {
    const rel = relative(pluginDir, f);
    parts.push(`===FILE: ${rel}===\n${await readFile(f, 'utf8')}\n===ENDFILE===`);
  }
  const readme = join(pluginDir, 'readme.txt');
  try {
    parts.push(`===FILE: readme.txt===\n${await readFile(readme, 'utf8')}\n===ENDFILE===`);
  } catch {
    /* optional */
  }
  return parts.join('\n\n');
}

/**
 * Auto-fix mechanical WPCS style (spacing, Yoda, alignment, doc spacing) with phpcbf. This handles the
 * bulk of phpcs style errors that smaller local models can't format by hand, leaving the model to focus
 * on logic and security. phpcbf exits non-zero when it fixes things — that's expected, so ignore it.
 */
async function autofixStyle(harnessDir: string, pluginDir: string): Promise<void> {
  await exec(join(harnessDir, 'vendor', 'bin', 'phpcbf'), ['-q', `--standard=${join(harnessDir, 'phpcs.xml.dist')}`, pluginDir], {
    timeoutMs: 120_000,
  });
}

const PROTOCOL = `OUTPUT PROTOCOL (strict): For every file you create or modify, output the ENTIRE file content
between markers, like:
===FILE: relative/path.php===
<the complete file content>
===ENDFILE===
Output nothing outside these blocks except a one-line summary at the very end. Paths are relative to the
plugin folder. Include the whole file, not a diff. Do not wrap file contents in markdown code fences.`;

async function generateSpec(llm: Platform, specText: string, usage: Usage): Promise<StructuredSpec | null> {
  const schema = `Output ONLY a JSON object (in a \`\`\`json fence) with keys: slug (kebab-case), pluginName,
description, version ("1.0.0"), prefix (4-7 lowercase alnum, not wp/__/_), requiresWp ("6.0"), requiresPhp ("7.4"),
capabilities [], postTypes [], taxonomies [], adminPages [], shortcodes [], blocks [], restEndpoints [],
ajaxActions [], cronEvents [], widgets [], dataStorage, securityRequirements [], smokeAssertions []
(PHP expressions asserting built features exist; PREFER strict-boolean checks post_type_exists()/
taxonomy_exists()/shortcode_exists()/defined(), e.g. "shortcode_exists('x_hours')").
Only fill arrays for features the spec calls for; use [] otherwise. All keys use the prefix.`;
  let feedback = '';
  for (let attempt = 1; attempt <= 3; attempt++) {
    const out = await chat(
      llm,
      [
        { role: 'system', content: 'You convert loose WordPress plugin specs into strict JSON. Output only JSON.' },
        { role: 'user', content: `${schema}\n\nLOOSE SPEC:\n${specText}${feedback}` },
      ],
      usage,
    );
    const json = extractJson(out);
    if (!json) {
      feedback = '\n\nYour last reply had no JSON object. Output ONLY a single fenced JSON object.';
      continue;
    }
    try {
      const parsed = normalizeSpec(JSON.parse(json));
      const problems = validateSpec(parsed);
      if (problems.length === 0) return parsed as unknown as StructuredSpec;
      feedback = `\n\nYour previous JSON had these problems — fix them and output the full corrected JSON:\n- ${problems.join('\n- ')}`;
    } catch (e) {
      feedback = `\n\nYour previous reply was not valid JSON (${String(e)}). Output ONLY a single valid JSON object.`;
    }
  }
  return null;
}

export async function runBuildLocal(args: string[], env: BuildEnv): Promise<number> {
  const specPath = args[0];
  if (!specPath) {
    console.error('Usage: npm run build-local -- specs/<name>.md');
    return 2;
  }
  const { repoRoot, harnessDir } = env;
  const cfg = ollamaConfig(); // embeddings for RAG
  const llm = platformFromEnv(); // the generation model
  const engineLabel = `${llm.protocol} (${llm.model}) @ ${llm.baseUrl}`;

  const health = await testPlatform(llm);
  console.log(`\nEngine: ${engineLabel}`);
  if (!health.ok) {
    console.error(`✖ ${health.message}`);
    return 2;
  }
  if (!(await dockerAvailable())) {
    console.error('Docker/wp-env is unavailable — cannot verify. Aborting.');
    return 2;
  }

  const rules = await loadRules(repoRoot);
  const specText = await readFile(specPath, 'utf8');
  const usage: Usage = { promptTokens: 0, completionTokens: 0, calls: 0 };
  const startTs = Date.now();

  // 1. Spec (local).
  console.log('\n▶ spec (local model)…');
  const spec = await generateSpec(llm, specText, usage);
  if (!spec) {
    console.error('The model could not produce a valid structured spec. Try a larger model.');
    return 1;
  }
  console.log(`  ${spec.pluginName} (slug ${spec.slug}, prefix ${spec.prefix})`);

  // 2. Scaffold.
  const pluginDir = await scaffoldPlugin(spec, repoRoot);
  console.log(`▶ scaffolded build/${spec.slug}/`);

  // 3. RAG index + retrieval (rules) + exemplar retrieval (verified whole-plugin templates).
  // RAG embeddings run on Ollama (OLLAMA_HOST). If it's unavailable — e.g. a cloud-only platform on a box
  // with no Ollama — build without retrieved rules rather than fail; the full rules are in the prompt anyway.
  console.log('▶ building/loading RAG index…');
  let idx: RagIndex | null = null;
  try {
    idx = await loadOrBuildIndex(cfg, repoRoot, (m) => console.log('  ' + m));
  } catch (e) {
    console.log(`  RAG unavailable (${e instanceof Error ? e.message : String(e)}) — continuing without it.`);
  }
  const rag = async (q: string, k: number): Promise<string> =>
    idx ? formatContext(await retrieve(idx, cfg, q, k).catch(() => [])) : '';
  const ragQuery = `${spec.description} ${spec.securityRequirements.join(' ')} WordPress plugin security escaping sanitizing nonce capability`;
  const ragCtx = await rag(ragQuery, 5);

  // Seed the corpus from examples on first run so exemplar retrieval has templates.
  if ((await readManifest(repoRoot)).entries.length === 0) {
    console.log('▶ seeding corpus from examples/…');
    await seedCorpus(repoRoot, (m) => console.log(m));
  }
  const exemplars = await selectExemplars(repoRoot, cfg, spec, 2);
  const exemplarCtx = formatExemplars(exemplars);
  if (exemplars.length) console.log(`▶ exemplars: ${exemplars.map((e) => `${e.entry.slug}(${e.score.toFixed(2)})`).join(', ')}`);

  const wpEnv = new WpEnv(repoRoot, pluginDir, spec.slug, harnessDir);
  const verify = async (): Promise<PipelineResult> =>
    runPipeline(pluginDir, {
      repoRoot,
      harnessDir,
      wpEnv,
      onGate: (r) => process.stdout.write(`    [${r.skipped ? 'SKIP' : r.passed ? 'PASS' : 'FAIL'}] ${r.label}\n`),
    });

  const systemPrompt = `You are an expert WordPress plugin developer. Write secure, WordPress-Coding-Standards-clean
PHP. Follow these hard rules exactly:\n\n${rules}\n\n${PROTOCOL}`;

  // 4. Initial implementation.
  console.log('\n▶ coder (file protocol): implementing…');
  const scaffold = await snapshotFiles(pluginDir);
  const firstOut = await chat(
    llm,
    [
      { role: 'system', content: systemPrompt },
      {
        role: 'user',
        content:
          `Implement this plugin. SPEC:\n${JSON.stringify(spec, null, 2)}\n\n` +
          `The scaffold already exists (below). Modify/extend it and output every file you change.\n\n` +
          (exemplarCtx ? exemplarCtx + '\n\n' : '') +
          `RELEVANT RULES:\n${ragCtx}\n\n` +
          `CURRENT SCAFFOLD:\n${scaffold}\n\n` +
          `Implement all features from the SPEC, update tests/test-smoke.php with the smokeAssertions, and ` +
          `update uninstall.php to remove stored data. Escape all output, sanitize all input, and pair every ` +
          `state-changing action with a nonce AND current_user_can(). Output files using the protocol.`,
      },
    ],
    usage,
  );
  let parsed = parseFiles(firstOut);
  let wr = await writeGeneratedFiles(parsed, pluginDir);
  console.log(`  wrote ${wr.written.length} file(s)${wr.rejected.length ? `, rejected ${wr.rejected.length} out-of-scope` : ''}.`);
  await autofixStyle(harnessDir, pluginDir);

  // 5. Verify → fix loop (RAG-augmented).
  console.log('\n▶ Verifying…');
  let pipe = await verify();
  let iterations = 0;
  while (!pipe.passed && iterations < MAX_FIX_ITERATIONS) {
    iterations++;
    const failing = pipe.results.filter((r) => !r.passed && !r.skipped);
    // Include r.notes: gates like phpunit put the real detail (assertion/fatal text) there, not in errors.
    const digest = failing
      .map((r) => `### ${r.label}\n` + r.errors.slice(0, 12).map((e) => `- ${e}`).join('\n') + (r.notes.length ? '\n' + r.notes.join('\n') : ''))
      .join('\n');
    const allErrors = failing.flatMap((r) => [...r.errors, ...r.notes]);
    const canonicalFixes = formatFixes(allErrors);
    const fixQuery = 'fix ' + failing.map((r) => r.label).join(' ') + ' ' + failing.flatMap((r) => r.errors.slice(0, 3)).join(' ');
    const fixCtx = (canonicalFixes ? canonicalFixes + '\n\n' : '') + (await rag(fixQuery, 4));
    console.log(`\n  Harness failed (${failing.map((r) => r.gate).join(', ')}) — fix iteration ${iterations}/${MAX_FIX_ITERATIONS}.`);
    const snap = await snapshotFiles(pluginDir);
    const example = '===FILE: ' + spec.slug + '.php===\n<?php\n// full corrected file here\n===ENDFILE===';
    const fixMsg = (extra: string): ChatMessage[] => [
      { role: 'system', content: systemPrompt },
      {
        role: 'user',
        content:
          `The verification harness FAILED with these errors. Return the corrected FULL content of ONLY the ` +
          `files that need changes, each wrapped EXACTLY like this (no prose, no markdown fences):\n${example}\n${extra}\n\n` +
          `ERRORS:\n${digest}\n\nRELEVANT RULES:\n${fixCtx}\n\nCURRENT FILES:\n${snap}`,
      },
    ];
    let out = await chat(llm, fixMsg(''), usage);
    parsed = parseFiles(out);
    if (parsed.length === 0) {
      // Local models sometimes forget the protocol on a fix turn — remind once.
      out = await chat(llm, fixMsg(' You MUST wrap every file in the ===FILE:path=== ... ===ENDFILE=== markers.'), usage);
      parsed = parseFiles(out);
    }
    if (parsed.length === 0) {
      console.log('  (model produced no file blocks after a retry — stopping the loop)');
      break;
    }
    wr = await writeGeneratedFiles(parsed, pluginDir);
    console.log(`  wrote ${wr.written.length} file(s).`);
    await autofixStyle(harnessDir, pluginDir);
    console.log('\n▶ Re-verifying…');
    pipe = await verify();
  }

  // 6. Package if green; always write a report. On success, feed the flywheel.
  const durMin = ((Date.now() - startTs) / 60000).toFixed(1);
  let zipPath: string | null = null;
  if (pipe.passed) {
    await mkdir(join(repoRoot, 'dist'), { recursive: true });
    zipPath = join(repoRoot, 'dist', `${spec.slug}.${spec.version}.zip`);
    const pkg = await exec('php', [join(harnessDir, 'bin', 'package.php'), pluginDir, spec.slug, zipPath], { timeoutMs: 60_000 });
    if (pkg.code === 0) console.log('\n▶ Packaged: ' + pkg.stdout.trim());
    else zipPath = null;
    await addToCorpus(repoRoot, pluginDir, spec);
    console.log(`▶ Added ${spec.slug} to the verified corpus (flywheel).`);
  } else {
    console.log('\n' + renderTerminal(pipe));
  }

  const reportLines = [
    `# Local build report: ${spec.pluginName}`,
    '',
    `- **Engine:** \`${engineLabel}\`  ·  **RAG:** ${idx ? `\`${cfg.embedModel}\`` : 'unavailable'}`,
    `- **Slug:** \`${spec.slug}\`  ·  **Version:** ${spec.version}`,
    `- **Outcome:** ${pipe.passed && zipPath ? '✅ passed all gates — .zip produced' : '❌ did not pass all gates'}`,
    `- **Iterations:** ${iterations}  ·  **Duration:** ${durMin} min  ·  **Calls:** ${usage.calls} (${usage.promptTokens}+${usage.completionTokens} tokens)`,
    '',
    renderMarkdown(pipe),
  ];
  await mkdir(join(repoRoot, 'dist'), { recursive: true });
  await writeFile(join(repoRoot, 'dist', `${spec.slug}-local-report.md`), reportLines.join('\n') + '\n', 'utf8');
  await emitResult({ ok: pipe.passed, engine: 'local', slug: spec.slug, pluginName: spec.pluginName, version: spec.version, zip: zipPath, report: join(repoRoot, 'dist', `${spec.slug}-local-report.md`), iterations, provides: summarizeProvides(spec), error: pipe.passed ? undefined : 'did not pass all gates' });

  console.log('\n' + '='.repeat(64));
  console.log(`${pipe.passed ? '✔' : '✖'} BUILD: ${spec.pluginName} (${engineLabel})`);
  console.log(`   gates: ${pipe.results.filter((r) => r.passed).length}/${pipe.results.length} pass · iterations: ${iterations} · ${durMin} min`);
  if (zipPath) console.log(`   .zip:   ${zipPath}`);
  console.log(`   report: dist/${spec.slug}-local-report.md`);
  console.log('='.repeat(64));
  return pipe.passed ? 0 : 1;
}
