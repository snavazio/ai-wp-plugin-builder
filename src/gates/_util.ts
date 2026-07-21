import type { GateResult } from '../types.js';

export function emptyResult(gate: string, label: string, requiresDocker: boolean): GateResult {
  return {
    gate,
    label,
    passed: false,
    skipped: false,
    errors: [],
    warnings: [],
    notes: [],
    durationMs: 0,
    requiresDocker,
  };
}

/** Finalize: passed = no errors (unless skipped). */
export function finalize(r: GateResult, start: number): GateResult {
  r.durationMs = Date.now() - start;
  if (!r.skipped) {
    r.passed = r.errors.length === 0;
  }
  return r;
}

export function skipped(gate: string, label: string, requiresDocker: boolean, reason: string, start: number): GateResult {
  const r = emptyResult(gate, label, requiresDocker);
  r.skipped = true;
  r.passed = false;
  r.notes.push(reason);
  r.durationMs = Date.now() - start;
  return r;
}
