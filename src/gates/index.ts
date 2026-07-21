/**
 * The ordered gate list — cheap (no Docker) first, expensive (wp-env) later.
 */
import type { GateDefinition } from '../types.js';
import { phpLintGate } from './phpLint.js';
import { staticStructureGate } from './staticStructure.js';
import { phpcsGate } from './phpcs.js';
import { phpstanGate } from './phpstan.js';
import { pluginCheckGate } from './pluginCheck.js';
import { activateGate } from './activate.js';
import { phpunitGate } from './phpunit.js';
import { versionConsistencyGate } from './versionConsistency.js';

export const GATES: GateDefinition[] = [
  phpLintGate, // 1
  staticStructureGate, // 2
  phpcsGate, // 3
  phpstanGate, // 4
  pluginCheckGate, // 5 (docker)
  activateGate, // 6 (docker)
  phpunitGate, // 7 (docker)
  versionConsistencyGate, // 8
];
