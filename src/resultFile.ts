/**
 * When AIWPB_RESULT_FILE is set (the builder service sets it per job), write a machine-readable
 * summary of the build so the service can report status + locate the .zip without scraping stdout.
 */
import { writeFile } from 'node:fs/promises';

export interface BuildResult {
  ok: boolean;
  engine: 'claude' | 'local';
  slug?: string;
  pluginName?: string;
  version?: string;
  zip?: string | null;
  report?: string | null;
  iterations?: number;
  costUsd?: number;
  error?: string;
}

export async function emitResult(result: BuildResult): Promise<void> {
  const path = process.env.AIWPB_RESULT_FILE;
  if (!path) return;
  try {
    await writeFile(path, JSON.stringify(result, null, 2), 'utf8');
  } catch {
    /* best effort */
  }
}
