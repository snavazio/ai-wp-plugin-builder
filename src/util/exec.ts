/**
 * Small promise wrapper around child_process for running external tools.
 * Never throws on non-zero exit — callers inspect `code`.
 */
import { spawn } from 'node:child_process';

export interface ExecResult {
  code: number;
  stdout: string;
  stderr: string;
  /** True if the process could not be spawned at all (ENOENT etc.). */
  spawnError?: string;
  durationMs: number;
}

export interface ExecOptions {
  cwd?: string;
  env?: NodeJS.ProcessEnv;
  /** Milliseconds before the process is killed. */
  timeoutMs?: number;
  /** Optional stdin to write. */
  input?: string;
}

export function exec(command: string, args: string[], opts: ExecOptions = {}): Promise<ExecResult> {
  const start = Date.now();
  return new Promise((resolve) => {
    let stdout = '';
    let stderr = '';
    let settled = false;

    const child = spawn(command, args, {
      cwd: opts.cwd,
      env: opts.env ?? process.env,
      shell: false,
    });

    const finish = (result: Omit<ExecResult, 'durationMs'>) => {
      if (settled) return;
      settled = true;
      clearTimeout(timer);
      resolve({ ...result, durationMs: Date.now() - start });
    };

    const timer = opts.timeoutMs
      ? setTimeout(() => {
          child.kill('SIGKILL');
          finish({ code: 124, stdout, stderr: stderr + `\n[timed out after ${opts.timeoutMs}ms]` });
        }, opts.timeoutMs)
      : (undefined as unknown as NodeJS.Timeout);

    child.stdout?.on('data', (d) => {
      stdout += d.toString();
    });
    child.stderr?.on('data', (d) => {
      stderr += d.toString();
    });
    child.on('error', (err) => {
      finish({ code: 127, stdout, stderr: stderr + String(err), spawnError: String(err) });
    });
    child.on('close', (code) => {
      finish({ code: code ?? 0, stdout, stderr });
    });

    if (opts.input !== undefined) {
      child.stdin?.write(opts.input);
      child.stdin?.end();
    }
  });
}

/** Recursively collect all *.php files under a directory. */
export async function findPhpFiles(dir: string): Promise<string[]> {
  const { readdir } = await import('node:fs/promises');
  const { join } = await import('node:path');
  const out: string[] = [];
  const skip = new Set(['vendor', 'node_modules', '.git', 'dist', 'build']);
  async function walk(current: string): Promise<void> {
    const entries = await readdir(current, { withFileTypes: true });
    for (const e of entries) {
      if (e.isDirectory()) {
        if (skip.has(e.name)) continue;
        await walk(join(current, e.name));
      } else if (e.isFile() && e.name.endsWith('.php')) {
        out.push(join(current, e.name));
      }
    }
  }
  await walk(dir);
  return out.sort();
}
