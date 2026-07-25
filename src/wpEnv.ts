/**
 * Manager for the throwaway WordPress sandbox (@wordpress/env).
 *
 * The activate / pluginCheck / phpunit gates all need the SAME running instance
 * with the plugin-under-test mounted. This module owns:
 *   - writing the generated root .wp-env.json (maps the plugin + harness dir, enables debug log)
 *   - starting the instance idempotently
 *   - running commands in the `cli` and `tests-cli` containers
 *
 * The plugin is mounted at wp-content/plugins/<slug> in both the dev and tests instances.
 * The harness dir is mapped to wp-content/aiwpb-harness so the phpunit phar + generated
 * test config are reachable from inside the tests container.
 */
import { writeFile, readFile } from 'node:fs/promises';
import { join, isAbsolute } from 'node:path';
import { exec, type ExecResult } from './util/exec.js';

const WP_ENV_BIN = 'node_modules/.bin/wp-env';
export const HARNESS_MOUNT = 'wp-content/aiwpb-harness';

export class WpEnv {
  private started = false;
  constructor(
    private readonly repoRoot: string,
    private readonly pluginDir: string,
    private readonly slug: string,
    private readonly harnessDir: string,
  ) {}

  private bin(): string {
    return join(this.repoRoot, WP_ENV_BIN);
  }

  /** Write the generated .wp-env.json at repo root mapping this plugin + the harness dir. */
  async writeConfig(): Promise<void> {
    // Single-box demo convenience: keep a "host" plugin (the AI Plugin Builder admin UI) mounted +
    // active across every build so a live Generate from that same wp-env doesn't drop it. Opt-in via
    // AIWPB_HOST_PLUGIN (comma-separated repo-relative or absolute paths). Off by default → no change.
    const hostPlugins = (process.env.AIWPB_HOST_PLUGIN || '')
      .split(',')
      .map((p) => p.trim())
      .filter(Boolean)
      .map((p) => (isAbsolute(p) ? p : join(this.repoRoot, p)))
      .filter((p) => p !== this.pluginDir);

    const config = {
      $schema: 'https://schemas.wp.org/trunk/wp-env.json',
      core: null,
      phpVersion: '8.1',
      plugins: [this.pluginDir, ...hostPlugins],
      mappings: {
        [HARNESS_MOUNT]: this.harnessDir,
      },
      config: {
        WP_DEBUG: true,
        WP_DEBUG_LOG: true,
        WP_DEBUG_DISPLAY: false,
        SCRIPT_DEBUG: true,
      },
    };
    await writeFile(join(this.repoRoot, '.wp-env.json'), JSON.stringify(config, null, 2) + '\n', 'utf8');
  }

  /** Start (or reconfigure) the instance. Idempotent within a run. */
  async start(): Promise<ExecResult> {
    await this.writeConfig();
    const res = await exec(this.bin(), ['start'], {
      cwd: this.repoRoot,
      timeoutMs: 20 * 60_000,
    });
    this.started = res.code === 0;
    return res;
  }

  isStarted(): boolean {
    return this.started;
  }

  /** Run an arbitrary command inside a container (`cli` or `tests-cli`). */
  async run(container: 'cli' | 'tests-cli', argv: string[], extraArgs: string[] = []): Promise<ExecResult> {
    return exec(this.bin(), ['run', container, ...extraArgs, ...argv], {
      cwd: this.repoRoot,
      timeoutMs: 10 * 60_000,
    });
  }

  /** Convenience: run a wp-cli command in the `cli` container. */
  async wp(args: string[]): Promise<ExecResult> {
    return this.run('cli', ['wp', ...args]);
  }

  /** Read the debug.log from inside the dev container; returns '' if absent. */
  async readDebugLog(): Promise<string> {
    const res = await this.run('cli', ['sh', '-c', 'cat wp-content/debug.log 2>/dev/null || true']);
    return res.stdout;
  }

  /** Truncate the debug.log so we only see fatals produced by the current activation. */
  async clearDebugLog(): Promise<void> {
    await this.run('cli', ['sh', '-c', 'rm -f wp-content/debug.log || true']);
  }

  get pluginSlug(): string {
    return this.slug;
  }
}

/** Detect whether Docker is available and its daemon is running. */
export async function dockerAvailable(): Promise<boolean> {
  const res = await exec('docker', ['info'], { timeoutMs: 15_000 });
  return res.code === 0 && !res.spawnError;
}

/** Read a text file, returning '' if missing. */
export async function readFileSafe(path: string): Promise<string> {
  try {
    return await readFile(path, 'utf8');
  } catch {
    return '';
  }
}
