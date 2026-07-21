/**
 * Shared types for the verification harness.
 */
import type { WpEnv } from './wpEnv.js';

export interface GateResult {
  /** Stable gate id, e.g. "phpLint". */
  gate: string;
  /** Human label shown in the report. */
  label: string;
  /** True only if the gate ran and found no hard failures. */
  passed: boolean;
  /** True if the gate was intentionally not run (e.g. Docker unavailable, or short-circuited). */
  skipped: boolean;
  /** Hard-failure messages. Non-empty => passed must be false. */
  errors: string[];
  /** Non-fatal issues worth surfacing. */
  warnings: string[];
  /** Freeform extra info (e.g. counts, tool versions, raw notes). */
  notes: string[];
  /** Wall-clock duration for the gate. */
  durationMs: number;
  /** True if this gate requires the wp-env / Docker sandbox. */
  requiresDocker: boolean;
}

export interface GateContext {
  /** Absolute path to the plugin directory under test. */
  pluginDir: string;
  /** Plugin slug (basename of pluginDir); also the expected text domain + zip root folder. */
  slug: string;
  /** Absolute path to the harness/ dir (phpcs.xml.dist, phpstan.neon, vendor/, bin/). */
  harnessDir: string;
  /** Absolute repo root. */
  repoRoot: string;
  /** Whether Docker/wp-env is available this run. */
  dockerAvailable: boolean;
  /** Shared wp-env handle for the Docker gates (undefined when Docker is unavailable). */
  wpEnv?: WpEnv;
}

export type GateFn = (ctx: GateContext) => Promise<GateResult>;

export interface GateDefinition {
  gate: string;
  label: string;
  requiresDocker: boolean;
  run: GateFn;
}

export interface PipelineResult {
  slug: string;
  pluginDir: string;
  passed: boolean;
  results: GateResult[];
  totalDurationMs: number;
  dockerAvailable: boolean;
}
