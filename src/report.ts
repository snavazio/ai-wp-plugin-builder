/**
 * Human-readable rendering of pipeline results (terminal table + optional markdown).
 */
import type { GateResult, PipelineResult } from './types.js';

const GREEN = '\x1b[32m';
const RED = '\x1b[31m';
const YELLOW = '\x1b[33m';
const DIM = '\x1b[2m';
const BOLD = '\x1b[1m';
const RESET = '\x1b[0m';

function statusCell(r: GateResult): string {
  if (r.skipped) return `${YELLOW}SKIP${RESET}`;
  return r.passed ? `${GREEN}PASS${RESET}` : `${RED}FAIL${RESET}`;
}

function ms(n: number): string {
  return n >= 1000 ? `${(n / 1000).toFixed(1)}s` : `${n}ms`;
}

export function renderTerminal(result: PipelineResult): string {
  const lines: string[] = [];
  lines.push('');
  lines.push(`${BOLD}Plugin:${RESET} ${result.slug}   ${DIM}(${result.pluginDir})${RESET}`);
  if (!result.dockerAvailable) {
    lines.push(`${YELLOW}Docker/wp-env unavailable — activation, Plugin Check, and smoke test were skipped.${RESET}`);
  }
  lines.push('');
  lines.push(`  ${'GATE'.padEnd(28)} ${'STATUS'.padEnd(6)} ${'TIME'.padEnd(8)}`);
  lines.push(`  ${'-'.repeat(28)} ${'-'.repeat(6)} ${'-'.repeat(8)}`);
  for (const r of result.results) {
    lines.push(`  ${r.label.padEnd(28)} ${statusCell(r).padEnd(6 + 9)} ${ms(r.durationMs).padEnd(8)}`);
  }
  lines.push('');

  // Details for anything that isn't a clean pass.
  for (const r of result.results) {
    if (r.passed && r.warnings.length === 0) continue;
    const header = r.skipped ? `${YELLOW}● ${r.label} (skipped)` : r.passed ? `${GREEN}● ${r.label}` : `${RED}● ${r.label} FAILED`;
    lines.push(`${header}${RESET}`);
    for (const e of r.errors) lines.push(`    ${RED}✗${RESET} ${e}`);
    for (const w of r.warnings.slice(0, 15)) lines.push(`    ${YELLOW}!${RESET} ${w}`);
    if (r.warnings.length > 15) lines.push(`    ${DIM}… ${r.warnings.length - 15} more warnings${RESET}`);
    for (const n of r.notes) lines.push(`    ${DIM}${n}${RESET}`);
    lines.push('');
  }

  const overall = result.passed ? `${GREEN}${BOLD}OVERALL: PASS${RESET}` : `${RED}${BOLD}OVERALL: FAIL${RESET}`;
  const firstFail = result.results.find((r) => !r.passed && !r.skipped);
  lines.push(`${overall}   ${DIM}total ${ms(result.totalDurationMs)}${RESET}`);
  if (firstFail) lines.push(`${DIM}First failing gate:${RESET} ${firstFail.label}`);
  lines.push('');
  return lines.join('\n');
}

export function renderMarkdown(result: PipelineResult): string {
  const lines: string[] = [];
  lines.push(`## Verification: \`${result.slug}\``);
  lines.push('');
  lines.push(`**Overall: ${result.passed ? '✅ PASS' : '❌ FAIL'}**  (total ${ms(result.totalDurationMs)})`);
  lines.push('');
  lines.push('| Gate | Status | Time | Errors | Warnings |');
  lines.push('| --- | --- | --- | --- | --- |');
  for (const r of result.results) {
    const status = r.skipped ? '⏭️ skip' : r.passed ? '✅ pass' : '❌ fail';
    lines.push(`| ${r.label} | ${status} | ${ms(r.durationMs)} | ${r.errors.length} | ${r.warnings.length} |`);
  }
  lines.push('');
  for (const r of result.results) {
    if (r.errors.length === 0 && r.warnings.length === 0 && r.notes.length === 0) continue;
    lines.push(`### ${r.label}`);
    if (r.errors.length) {
      lines.push('**Errors:**');
      for (const e of r.errors) lines.push(`- ${e}`);
    }
    if (r.warnings.length) {
      lines.push('**Warnings:**');
      for (const w of r.warnings.slice(0, 25)) lines.push(`- ${w}`);
    }
    if (r.notes.length) {
      for (const n of r.notes) lines.push(`- _${n}_`);
    }
    lines.push('');
  }
  return lines.join('\n');
}
