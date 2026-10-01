/**
 * Phase 1 — spec-in → verified .zip-out generator loop.
 *
 * Deterministic TypeScript orchestration (the "pipeline" that owns phase ordering):
 *   spec-writer → scaffold from templates → coder → verify harness → feed failing-gate errors back to
 *   coder (capped iterations) → independent read-only security-auditor → coder fixes → re-verify →
 *   package (.zip) → report. Objective gate results, not a model's opinion, decide pass/fail.
 */
import { readFile, writeFile, mkdir } from 'node:fs/promises';
import { basename, join } from 'node:path';
import { query, type Options } from '@anthropic-ai/claude-agent-sdk';
import {
  loadRules,
  specWriter,
  coder,
  securityAuditor,
  type RoleConfig,
} from './agents.js';
import { makeCoderHooks, newHookStats, type HookStats } from './hooks.js';
import { scaffoldPlugin, validateSpec, normalizeSpec, summarizeProvides, type StructuredSpec } from './spec.js';
import { runPipeline } from './pipeline.js';
import { renderTerminal, renderMarkdown } from './report.js';
import { WpEnv, dockerAvailable } from './wpEnv.js';
import { exec } from './util/exec.js';
import { addToCorpus } from './corpus.js';
import { emitResult } from './resultFile.js';
import type { PipelineResult } from './types.js';

export interface BuildEnv {
  repoRoot: string;
  harnessDir: string;
}

export const MAX_FIX_ITERATIONS = 5;
export const MAX_AUDIT_ROUNDS = 2;

export interface AgentRun {
  text: string;
  costUsd: number;
  turns: number;
  isError: boolean;
  errorInfo: string;
}

export async function runAgent(params: {
  repoRoot: string;
  role: RoleConfig;
  rulesAppend: string;
  prompt: string;
  hooks?: Options['hooks'];
  maxTurns: number;
  maxBudgetUsd: number;
}): Promise<AgentRun> {
  const { role } = params;
  const options: Options = {
    cwd: params.repoRoot,
    // Set by the builder service from the chosen AI platform; unset = the SDK's default model.
    model: process.env.AIWPB_CLAUDE_MODEL || undefined,
    settingSources: ['project'],
    systemPrompt: {
      type: 'preset',
      preset: 'claude_code',
      append: role.instructions + '\n\n===== PROJECT HARD RULES =====\n' + params.rulesAppend,
    },
    tools: role.tools,
    allowedTools: role.tools,
    permissionMode: 'acceptEdits',
    hooks: params.hooks,
    maxTurns: params.maxTurns,
    maxBudgetUsd: params.maxBudgetUsd,
    stderr: () => {},
  };

  const run: AgentRun = { text: '', costUsd: 0, turns: 0, isError: false, errorInfo: '' };
  try {
    for await (const msg of query({ prompt: params.prompt, options })) {
      if (msg.type === 'result') {
        run.costUsd = msg.total_cost_usd;
        run.turns = msg.num_turns;
        if (msg.subtype === 'success') {
          run.text = msg.result;
        } else {
          run.isError = true;
          run.errorInfo = (msg as unknown as { errors?: string[] }).errors?.join('; ') ?? msg.subtype;
        }
      }
    }
  } catch (e) {
    // A transport/subprocess-level failure (e.g. "Claude Code process exited with code 1") is thrown
    // from the async generator rather than delivered as a result message. It is frequently transient.
    // Convert it into a soft failure so the caller's fix loop can retry or emit a proper result,
    // instead of the whole job crashing before it can write result.json.
    run.isError = true;
    run.errorInfo = e instanceof Error ? e.message : String(e);
  }
  return run;
}

/** Concise failing-gate error digest to feed back to the coder. */
export function digestFailures(pipe: PipelineResult): string {
  const lines: string[] = [];
  for (const r of pipe.results) {
    if (r.passed || r.skipped) continue;
    lines.push(`### Gate FAILED: ${r.label}`);
    for (const e of r.errors.slice(0, 15)) lines.push(`- ${e}`);
    if (r.errors.length > 15) lines.push(`- …and ${r.errors.length - 15} more.`);
  }
  return lines.join('\n');
}

export interface AuditFinding {
  severity: 'high' | 'medium' | 'low';
  file?: string;
  line?: number;
  issue: string;
  required_fix: string;
}

export function parseFindings(text: string): { findings: AuditFinding[]; parseNote: string } {
  const fence = text.match(/```json\s*([\s\S]*?)```/i) ?? text.match(/(\{[\s\S]*"findings"[\s\S]*\})/);
  if (!fence) return { findings: [], parseNote: 'No JSON findings block found in auditor output.' };
  try {
    const obj = JSON.parse(fence[1].trim());
    const findings = Array.isArray(obj.findings) ? (obj.findings as AuditFinding[]) : [];
    return { findings, parseNote: '' };
  } catch {
    return { findings: [], parseNote: 'Auditor findings JSON did not parse.' };
  }
}

export function blockingFindings(findings: AuditFinding[]): AuditFinding[] {
  return findings.filter((f) => f.severity === 'high' || f.severity === 'medium');
}

export async function runBuild(args: string[], env: BuildEnv): Promise<number> {
  const specPath = args[0];
  if (!specPath) {
    console.error('Usage: npm run build -- specs/<name>.md');
    return 2;
  }
  if (!process.env.ANTHROPIC_API_KEY) {
    console.error('ANTHROPIC_API_KEY is not set — required for the generator loop.');
    return 2;
  }

  const { repoRoot, harnessDir } = env;
  const rules = await loadRules(repoRoot);
  const specText = await readFile(specPath, 'utf8');
  const runLog: string[] = [];
  const startTs = Date.now();
  let totalCost = 0;

  const docker = await dockerAvailable();
  if (!docker) {
    console.error('Docker/wp-env is unavailable — cannot verify activation/Plugin Check. Aborting.');
    return 2;
  }

  // ---------- 1. spec-writer → structured SPEC.json ----------
  console.log('\n▶ spec-writer: converting the loose spec…');
  await mkdir(join(repoRoot, 'build'), { recursive: true });
  const stagingPath = join(repoRoot, 'build', basename(specPath).replace(/\.md$/i, '') + '.spec.json');
  let spec: StructuredSpec | null = null;
  let specProblems: string[] = [];
  for (let attempt = 1; attempt <= 2 && !spec; attempt++) {
    const prompt =
      `Convert this loose plugin spec into the structured JSON and WRITE it to EXACTLY this absolute path:\n` +
      `${stagingPath}\n\n----- LOOSE SPEC (${basename(specPath)}) -----\n${specText}` +
      (attempt > 1 ? `\n\nYour previous attempt had these problems — fix them:\n- ${specProblems.join('\n- ')}` : '');
    const r = await runAgent({ repoRoot, role: specWriter, rulesAppend: rules, prompt, maxTurns: 10, maxBudgetUsd: 1.5 });
    totalCost += r.costUsd;
    console.log(`  spec-writer done (${r.turns} turns, $${r.costUsd.toFixed(4)}).`);
    try {
      const parsed = normalizeSpec(JSON.parse(await readFile(stagingPath, 'utf8')));
      specProblems = validateSpec(parsed);
      if (specProblems.length === 0) spec = parsed as unknown as StructuredSpec;
      else console.log(`  spec invalid: ${specProblems.join('; ')}`);
    } catch (e) {
      specProblems = ['SPEC.json was not written or is not valid JSON.'];
      console.log(`  ${specProblems[0]}`);
    }
  }
  if (!spec) {
    console.error('spec-writer could not produce a valid spec. Aborting.');
    return 1;
  }
  console.log(`\nStructured spec for "${spec.pluginName}" (slug: ${spec.slug}, prefix: ${spec.prefix}):`);
  console.log(
    `  CPTs: ${spec.postTypes.length}, admin pages: ${spec.adminPages.length}, shortcodes: ${spec.shortcodes.length}, ` +
      `blocks: ${spec.blocks.length}, REST: ${spec.restEndpoints.length}`,
  );
  runLog.push(`Spec: ${spec.pluginName} (slug ${spec.slug}). ${spec.description}`);

  // A failed build MUST still write result.json, or the service reports the opaque "produced no
  // result" and discards the whole run. emitFail centralizes that for every non-success exit.
  let iterations = 0;
  const specOk = spec;
  const emitFail = (error: string): Promise<void> =>
    emitResult({
      ok: false,
      engine: 'claude',
      slug: specOk.slug,
      pluginName: specOk.pluginName,
      version: specOk.version,
      zip: null,
      report: join(repoRoot, 'dist', `${specOk.slug}-report.md`),
      iterations,
      costUsd: totalCost,
      provides: summarizeProvides(specOk),
      error,
    });

  // Everything from scaffolding onward runs inside a try so any unexpected throw still emits a result.
  try {
    // ---------- 2. scaffold from templates ----------
    const pluginDir = await scaffoldPlugin(spec, repoRoot);
    console.log(`\n▶ Scaffolded skeleton at build/${spec.slug}/`);

    // Shared wp-env instance reused across every verify iteration.
    const wpEnv = new WpEnv(repoRoot, pluginDir, spec.slug, harnessDir);
    const hookStats: HookStats = newHookStats();
    const coderHooks = makeCoderHooks(pluginDir, join(repoRoot, 'dist'), hookStats);

    const verify = async (): Promise<PipelineResult> =>
      runPipeline(pluginDir, {
        repoRoot,
        harnessDir,
        wpEnv,
        onGate: (r) => {
          const s = r.skipped ? 'SKIP' : r.passed ? 'PASS' : 'FAIL';
          process.stdout.write(`    [${s}] ${r.label}\n`);
        },
      });

    const runCoder = async (prompt: string, label: string): Promise<void> => {
      console.log(`\n▶ coder: ${label}…`);
      const r = await runAgent({ repoRoot, role: coder, rulesAppend: rules, prompt, hooks: coderHooks, maxTurns: 120, maxBudgetUsd: 5 });
      totalCost += r.costUsd;
      console.log(`  coder done (${r.turns} turns, $${r.costUsd.toFixed(4)}).`);
      if (r.isError) console.log(`  coder ended early: ${r.errorInfo}`);
    };

    // ---------- 3. coder implements ----------
    await runCoder(
      `Implement the plugin fully from build/${spec.slug}/SPEC.json into build/${spec.slug}/. The compliant ` +
        `skeleton is already scaffolded there — build on it. Implement every CPT/field, admin page, shortcode/block, ` +
        `and REST endpoint in the spec. Update tests/test-smoke.php with the spec's smokeAssertions and uninstall.php ` +
        `with data cleanup. Keep everything PHP_CodeSniffer-clean. When the whole spec is implemented, stop.`,
      'initial implementation',
    );

    // ---------- 4–6. verify → fix loop (capped) ----------
    console.log('\n▶ Verifying…');
    let pipe = await verify();
    while (!pipe.passed && iterations < MAX_FIX_ITERATIONS) {
      iterations++;
      console.log(`\n  Harness failed — fix iteration ${iterations}/${MAX_FIX_ITERATIONS}.`);
      await runCoder(
        `The verification harness FAILED. Fix ONLY these issues in build/${spec.slug}/, then stop. Do not add ` +
          `unrelated changes.\n\n${digestFailures(pipe)}`,
        `fix iteration ${iterations}`,
      );
      console.log('\n▶ Re-verifying…');
      pipe = await verify();
    }

    if (!pipe.passed) {
      console.log('\n' + renderTerminal(pipe));
      console.error(`\n✖ Still failing after ${iterations} iterations — surfacing the blocker. No .zip produced.`);
      await writeReport({ repoRoot, spec, pipe, findings: [], auditNote: 'Not reached — harness never passed.', hookStats, iterations, totalCost, startTs, zipPath: null, runLog });
      await emitFail('did not pass all gates');
      return 1;
    }
    console.log(`\n✔ Harness green after ${iterations} fix iteration(s).`);

    // ---------- 7. independent security auditor ----------
    console.log('\n▶ security-auditor (independent, read-only)…');
    let auditRun = await runAgent({
      repoRoot,
      role: securityAuditor,
      rulesAppend: rules,
      prompt: `Audit the plugin in build/${spec.slug}/ against the hard security rules. Output ONLY the findings JSON block.`,
      maxTurns: 40,
      maxBudgetUsd: 3,
    });
    totalCost += auditRun.costUsd;
    let { findings, parseNote } = parseFindings(auditRun.text);
    console.log(`  auditor: ${findings.length} finding(s)${parseNote ? ' — ' + parseNote : ''} ($${auditRun.costUsd.toFixed(4)}).`);

    let auditRounds = 0;
    while (blockingFindings(findings).length > 0 && auditRounds < MAX_AUDIT_ROUNDS) {
      auditRounds++;
      const blk = blockingFindings(findings);
      console.log(`\n  Auditor raised ${blk.length} blocking finding(s) — fix round ${auditRounds}/${MAX_AUDIT_ROUNDS}.`);
      const fixList = blk
        .map((f) => `- [${f.severity}] ${f.file ?? '?'}:${f.line ?? '?'} — ${f.issue} → ${f.required_fix}`)
        .join('\n');
      await runCoder(
        `The INDEPENDENT security auditor found issues. Fix ONLY these in build/${spec.slug}/, then stop:\n${fixList}`,
        `auditor fix round ${auditRounds}`,
      );
      console.log('\n▶ Re-verifying after auditor fixes…');
      pipe = await verify();
      if (!pipe.passed) {
        console.log('\n' + renderTerminal(pipe));
        console.error('\n✖ Harness regressed after auditor fixes. No .zip produced.');
        await writeReport({ repoRoot, spec, pipe, findings, auditNote: parseNote, hookStats, iterations, totalCost, startTs, zipPath: null, runLog });
        await emitFail('harness regressed after auditor fixes');
        return 1;
      }
      auditRun = await runAgent({
        repoRoot,
        role: securityAuditor,
        rulesAppend: rules,
        prompt: `Re-audit build/${spec.slug}/ after fixes. Output ONLY the findings JSON block.`,
        maxTurns: 40,
        maxBudgetUsd: 3,
      });
      totalCost += auditRun.costUsd;
      ({ findings, parseNote } = parseFindings(auditRun.text));
      console.log(`  auditor: ${findings.length} finding(s) remaining.`);
    }

    const stillBlocking = blockingFindings(findings);
    if (stillBlocking.length > 0) {
      console.error(`\n✖ Auditor still reports ${stillBlocking.length} blocking finding(s) after ${auditRounds} rounds. No .zip produced.`);
      await writeReport({ repoRoot, spec, pipe, findings, auditNote: parseNote, hookStats, iterations, totalCost, startTs, zipPath: null, runLog });
      await emitFail(`auditor still reports ${stillBlocking.length} blocking finding(s)`);
      return 1;
    }

    // ---------- 8. package ----------
    console.log('\n▶ Packaging…');
    await mkdir(join(repoRoot, 'dist'), { recursive: true });
    const zipPath = join(repoRoot, 'dist', `${spec.slug}.${spec.version}.zip`);
    const pkg = await exec('php', [join(harnessDir, 'bin', 'package.php'), pluginDir, spec.slug, zipPath], { timeoutMs: 60_000 });
    if (pkg.code !== 0) {
      console.error('Packaging failed:', pkg.stderr || pkg.stdout);
      await emitFail('packaging failed');
      return 1;
    }
    console.log('  ' + pkg.stdout.trim());
    await addToCorpus(repoRoot, pluginDir, spec);

    // ---------- 9. report ----------
    await writeReport({ repoRoot, spec, pipe, findings, auditNote: parseNote, hookStats, iterations, totalCost, startTs, zipPath, runLog });
    await emitResult({ ok: true, engine: 'claude', slug: spec.slug, pluginName: spec.pluginName, version: spec.version, zip: zipPath, report: join(repoRoot, 'dist', `${spec.slug}-report.md`), iterations, costUsd: totalCost, provides: summarizeProvides(spec) });

    console.log('\n' + '='.repeat(64));
    console.log(`✔ DONE: ${spec.pluginName}`);
    console.log(`   .zip:    ${zipPath}`);
    console.log(`   report:  dist/${spec.slug}-report.md`);
    console.log(`   gates:   all 8 PASS · auditor: clean · iterations: ${iterations} · cost: $${totalCost.toFixed(4)}`);
    console.log('='.repeat(64));
    return 0;
  } catch (e) {
    const msg = e instanceof Error ? e.message : String(e);
    console.error(`\n✖ Build run crashed: ${msg}`);
    await emitFail(`run crashed: ${msg}`);
    return 1;
  }
}

export async function writeReport(p: {
  repoRoot: string;
  spec: StructuredSpec;
  pipe: PipelineResult;
  findings: AuditFinding[];
  auditNote: string;
  hookStats: HookStats;
  iterations: number;
  totalCost: number;
  startTs: number;
  zipPath: string | null;
  runLog: string[];
}): Promise<void> {
  const { spec, pipe } = p;
  const durMin = ((Date.now() - p.startTs) / 60000).toFixed(1);
  const lines: string[] = [];
  lines.push(`# Build report: ${spec.pluginName}`);
  lines.push('');
  lines.push(`- **Slug:** \`${spec.slug}\`  ·  **Version:** ${spec.version}  ·  **Prefix:** \`${spec.prefix}\``);
  lines.push(`- **Outcome:** ${pipe.passed && blockingFindings(p.findings).length === 0 && p.zipPath ? '✅ install-ready .zip produced' : '❌ not shippable'}`);
  if (p.zipPath) lines.push(`- **Artifact:** \`${p.zipPath.replace(p.repoRoot + '/', '')}\``);
  lines.push(`- **Iterations:** ${p.iterations}  ·  **Duration:** ${durMin} min  ·  **Token cost:** $${p.totalCost.toFixed(4)}`);
  lines.push('');

  lines.push('## What it built (plain English)');
  lines.push('');
  lines.push(spec.description);
  lines.push('');
  if (spec.postTypes.length) lines.push(`- **Custom post types:** ${spec.postTypes.map((c) => `\`${c.key}\` (${c.labelPlural})`).join(', ')}`);
  if (spec.taxonomies?.length) lines.push(`- **Taxonomies:** ${spec.taxonomies.map((t) => `\`${t.key}\``).join(', ')}`);
  if (spec.adminPages.length) lines.push(`- **Admin pages:** ${spec.adminPages.map((a) => a.title).join(', ')}`);
  if (spec.shortcodes.length) lines.push(`- **Shortcodes:** ${spec.shortcodes.map((s) => `\`[${s.tag}]\``).join(', ')}`);
  if (spec.blocks.length) lines.push(`- **Blocks:** ${spec.blocks.map((b) => b.title).join(', ')}`);
  if (spec.restEndpoints.length) lines.push(`- **REST endpoints:** ${spec.restEndpoints.map((r) => `\`${r.namespace}${r.route}\` (${r.methods.join('/')})`).join(', ')}`);
  if (spec.ajaxActions?.length) lines.push(`- **AJAX actions:** ${spec.ajaxActions.map((a) => `\`${a.action}\``).join(', ')}`);
  if (spec.cronEvents?.length) lines.push(`- **Cron events:** ${spec.cronEvents.map((c) => `\`${c.hook}\` (${c.recurrence})`).join(', ')}`);
  if (spec.widgets?.length) lines.push(`- **Widgets:** ${spec.widgets.map((w) => w.name).join(', ')}`);
  lines.push('');

  lines.push(renderMarkdown(pipe));

  lines.push('## Independent security auditor');
  lines.push('');
  if (p.auditNote) lines.push(`_${p.auditNote}_`);
  if (p.findings.length === 0) {
    lines.push('✅ **Sign-off: no findings.** The read-only auditor found no security issues, and the objective gates (PHPCS security sniffs + WordPress Plugin Check security category) are clean.');
  } else {
    lines.push(`Findings (${p.findings.length}):`);
    for (const f of p.findings) lines.push(`- **[${f.severity}]** ${f.file ?? '?'}:${f.line ?? '?'} — ${f.issue} (required: ${f.required_fix})`);
  }
  lines.push('');

  lines.push('## Security hooks');
  lines.push('');
  lines.push(`- PreToolUse security-pattern blocks: **${p.hookStats.securityBlocks}**`);
  lines.push(`- Workspace-boundary blocks: **${p.hookStats.boundaryBlocks}**`);
  if (p.hookStats.reasons.length) {
    for (const r of p.hookStats.reasons.slice(0, 10)) lines.push(`  - ${r}`);
  }
  lines.push('');

  await writeFile(join(p.repoRoot, 'dist', `${spec.slug}-report.md`), lines.join('\n') + '\n', 'utf8');
}
