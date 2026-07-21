# AI WP Plugin Builder

Internal, single-tenant, CLI-first agency tool. It takes a natural-language spec for a WordPress feature
and produces a **complete, security-audited, install-ready WordPress plugin `.zip`** — verified by actually
installing and activating it in a throwaway WordPress before it is considered done.

Not a SaaS. No auth, billing, multi-tenancy, or web UI by design.

- **Phase 0 — Verification harness:** run any plugin folder through an ordered set of gates and get a clear
  pass/fail with the failing gate named. This is the spine and the trust model.
- **Phase 1 — Generator loop:** `spec in → verified .zip out`, driven by the Claude Agent SDK with an
  independent, read-only security auditor. Objective external tools decide pass/fail, never a model's opinion.

## Prerequisites

- **Docker** running (for `@wordpress/env`). ~5 GB free disk for images (first run pulls them).
- **Node.js 20+** and npm.
- **PHP 8.x** and **Composer** on PATH.
- **WP-CLI** (`wp --info` works).
- `ANTHROPIC_API_KEY` in the environment (only needed for Phase 1 `build`).

Install once:

```bash
npm install
cd harness && composer install && cd ..
```

> **No Docker?** The Docker gates (Plugin Check, activate, smoke test) will be skipped and `verify` will
> report the run as incomplete. A `@wordpress/wp-now` (SQLite) fallback is the planned alternative — tell
> the tool and it will use that instead. The static gates (lint, structure, PHPCS, PHPStan, version) always
> run without Docker via `--no-docker`.

## Usage

### Verify an existing plugin folder

```bash
npm run verify -- <path-to-plugin-dir>            # full run (needs Docker)
npm run verify -- <path-to-plugin-dir> --no-docker # static gates only
npm run verify -- <path-to-plugin-dir> --md report.md  # also write a markdown report
```

Exits non-zero on failure. Try the samples:

```bash
npm run verify -- samples/good-plugin    # → PASS all gates
npm run verify -- samples/bad-plugin     # → FAIL; names the gate that caught each planted vuln
```

### Build a plugin from a spec (Phase 1)

```bash
npm run build -- specs/<name>.md         # → dist/<slug>.<version>.zip + dist/<slug>-report.md
```

Write a spec as a markdown file in `specs/` describing the feature in plain language (plugin name, custom
post types + fields, taxonomies, admin screens, shortcodes/blocks, REST endpoints, AJAX actions, cron
events, widgets, capabilities, data storage). The `spec-writer` agent turns it into a precise structured
spec before any code is written.

### Revise an existing plugin (Phase 2)

```bash
npm run revise -- <slug> "your change request" [--version X.Y.Z]
```

Applies a change to an already-built plugin (found in `examples/<slug>`, `build/<slug>`, or a path),
bumps the version everywhere (header, readme Stable tag, `*_VERSION`), and re-runs the full harness +
independent auditor before re-packaging. Same 8-gate bar as a fresh build.

### Batch verification & regression (Phase 3 — fully local, no API)

```bash
npm run verify-all -- examples            # verify every plugin under a dir; combined table
npm run verify-all -- examples --no-docker # static gates only (fast, offline)
npm run regression                        # golden-set: assert known gate OUTCOMES hold
npm run regression -- --static            # fast offline regression (static gates)
```

`regression` is the guardrail on the harness itself: it asserts the good sample passes, the bad sample
*fails on the specific gates that must catch it* (incl. that phpcs really reports a security error), and
every committed example stays green. Run it after any change to the gates or templates.

### Local generation on Ollama (Phase 4 — no API, runs offline)

```bash
npm run build-local -- specs/<name>.md
# point at a remote box (e.g. an Olares node) and/or a bigger model:
OLLAMA_HOST=http://<host>:11434 OLLAMA_MODEL=qwen2.5-coder:32b npm run build-local -- specs/x.md
```

Same loop as `build`, but generation runs on a **local Ollama model** (default `qwen2.5-coder:14b`)
with a small local **RAG** (`nomic-embed-text`) that feeds the WordPress rules + a clean reference plugin
to the model. The 8-gate harness is still the objective judge, so local output is held to the exact same
bar — and the report honestly shows where a smaller model falls short. Cost: $0. Config via env
(`OLLAMA_HOST`, `OLLAMA_MODEL`, `OLLAMA_EMBED_MODEL`).

**Suggested models:** `qwen2.5-coder:32b` (Q4 ≈20 GB VRAM — best local quality), `qwen2.5-coder:14b`
(portable default). Pull once with `ollama pull <model>`.

## The gates (cheap → expensive, fail early)

| # | Gate | Needs Docker | What it enforces |
| - | ---- | ------------ | ---------------- |
| 1 | `phpLint` | no | `php -l` clean on every `.php` file |
| 2 | `staticStructure` | no | ABSPATH guard, valid header, Text Domain == slug, uninstall guard |
| 3 | `phpcs` | no | WordPress standard incl. security sniffs (errors fail, warnings report) |
| 4 | `phpstan` | no | Level 5 with WordPress stubs |
| 5 | `pluginCheck` | yes | WordPress Plugin Check — **any security error = hard fail** |
| 6 | `activate` | yes | Install + activate in wp-env; zero PHP fatals in debug.log |
| 7 | `phpunit` | yes | PHPUnit smoke test |
| 8 | `versionConsistency` | no | header Version == readme Stable tag == `*_VERSION` constant |

Static gates always run (full static picture even on failure). Docker gates short-circuit after the first
hard failure.

## Definition of Done

A plugin is done only when it **installs and activates in a real WordPress with zero PHP fatals** AND
**passes WordPress Plugin Check with no security errors**. See `CLAUDE.md` for the full hard-rule set.

## Repository layout

```
src/            TypeScript engine: run.ts (CLI), pipeline.ts, gates/, report.ts, wpEnv.ts
harness/        PHP tooling: phpcs.xml.dist, phpstan.neon, composer.json, .wp-env.json, bin/phpunit.phar
samples/        good-plugin (clean quality bar) + bad-plugin (planted vulns)
specs/          your input specs (one markdown file per plugin)
examples/       committed example builds: generated source + BUILD-REPORT.md + .zip per plugin
build/          working dir for the plugin under construction (git-ignored)
dist/           finished .zip files + reports (git-ignored)
.claude/        CLAUDE.md rules, agent + command definitions
```

## Example builds (checked in)

`examples/` holds three plugins generated end-to-end by `npm run build` from the specs in `specs/`,
each with its full source, its `BUILD-REPORT.md` (gate table, auditor sign-off, iteration count, token
cost), and its distributable `.zip`:

- `examples/testimonials/` — Testimonials CPT with a star-rating field and admin Rating column.
- `examples/business-hours/` — a settings page + `[business_hours]` shortcode.
- `examples/events-rest/` — an Events CPT with a public read-only REST endpoint.

All three pass every gate (0 security errors, activates with no fatals, smoke test green) and re-verify
cleanly with `npm run verify -- examples/<slug>`.

## Adding a new gate

1. Create `src/gates/<name>.ts` exporting a `GateDefinition` (`{ gate, label, requiresDocker, run }`)
   whose `run(ctx)` returns a `GateResult`. Use `emptyResult` / `finalize` from `src/gates/_util.ts`.
2. Add it to the ordered list in `src/gates/index.ts` at the right cost position.
3. Docker gates read `ctx.wpEnv` (a shared, already-started `WpEnv`).

## Adding a new plugin type (Phase 1)

Extend the spec template and the `coder` agent guidance so the structured spec captures the new capability,
and make sure `harness/templates/` has any needed skeleton. The gates are type-agnostic and need no change.
