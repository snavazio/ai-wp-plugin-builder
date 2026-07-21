/**
 * Phase 1 — spec-in → verified .zip-out generator loop.
 * Stub for now; implemented after the Phase 0 harness is proven.
 */
export interface BuildEnv {
  repoRoot: string;
  harnessDir: string;
}

export async function runBuild(_args: string[], _env: BuildEnv): Promise<number> {
  console.error('The `build` generator loop is Phase 1 and is not implemented yet.');
  console.error('Phase 0 (the verification harness) is the current deliverable.');
  return 2;
}
