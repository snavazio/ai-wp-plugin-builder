/**
 * Filesystem helpers shared by the build, edit and publish flows.
 */
import { mkdir, readdir, rm } from 'node:fs/promises';
import { join } from 'node:path';

/**
 * Empty a directory but keep the directory itself. build/<slug> is bind-mounted into the wp-env sandbox;
 * deleting and recreating the folder leaves the container looking at a stale, empty mount (activation then
 * fails), so a fresh start must clear the folder's contents instead of the folder.
 */
export async function emptyDir(dir: string): Promise<void> {
  await mkdir(dir, { recursive: true });
  for (const e of await readdir(dir)) await rm(join(dir, e), { recursive: true, force: true });
}
