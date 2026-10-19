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
  /** Security-auditor findings left at the end (Claude engine only). */
  auditFindings?: number;
  /** Set by wporg-prepare. */
  wporg?: { assetsZip: string; wporgSlug: string; testedUpTo: string; username: string; blockers: string[] };
  /** Set by wporg-push on success. */
  wporgPush?: { versionId: number; version: string; revision: string; wporgSlug: string };
  /** Set by publish-github on success. */
  github?: { owner: string; repo: string; url: string; private: boolean; tag: string; commit: string; releaseUrl: string; versionId: number };
  /** Human-readable summary of what the plugin exposes + where to find it. */
  provides?: string;
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
