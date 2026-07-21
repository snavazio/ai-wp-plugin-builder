# AI WP Plugin Builder — Project Memory & Quality Bar

This is an **internal, single-tenant, CLI-first agency tool**. It turns a natural-language spec into a
**security-audited, install-ready WordPress plugin `.zip`**, verified by actually installing and
activating it in a throwaway WordPress before it is considered done.

**Do NOT add** auth, billing, multi-tenancy, or a web UI. Keep it simple, robust, well-documented.

## Architecture (don't drift from this)

- TypeScript orchestrator (`src/`) drives an ordered set of **verification gates** (`src/gates/`).
- Generation engine is the **Claude Agent SDK** with independent sub-agents.
- The **security auditor is a separate, read-only agent**. It never signs off on its own code and
  cannot mark itself passed. The source of truth for pass/fail is **objective external tools**
  (PHP_CodeSniffer, PHPStan, WordPress Plugin Check), never a model's opinion.
- Sandbox for testing generated plugins is **`@wordpress/env`** (Docker); fall back to `wp-now` if Docker is unavailable.

---

## HARD RULES every generated plugin MUST satisfy

These are non-negotiable and are exactly what the gates enforce. Detail lives in `.claude/rules/`.

### Structure — see `.claude/rules/structure.md`
- Valid plugin header in the main file: `Plugin Name`, `Version`, `License: GPLv2 or later`,
  `Text Domain` **equal to the plugin folder slug**, `Requires at least`, `Requires PHP`.
- Exactly one root folder in the `.zip`, named exactly the slug.
- Prefix every global function/class/constant with a unique 4–5+ char prefix.
  **Never** use `wp_`, `__`, `_` as prefixes.
- Proper `register_activation_hook` / `register_deactivation_hook`.
- `uninstall.php` present for data cleanup, with the mandatory guard:
  `if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { die; }`
- Valid `readme.txt` with `Stable tag` matching the header `Version`.
- All user-facing strings internationalized with the text domain **as a string literal** (never a variable).

### Security — see `.claude/rules/security.md` (EVERY item is a hard gate)
- `if ( ! defined( 'ABSPATH' ) ) { exit; }` at the top of **every** executable PHP file.
- **Sanitize every input** (`sanitize_text_field`, `absint`, `sanitize_email`, `esc_url_raw`,
  `wp_kses`, …) and `wp_unslash()` superglobals before use.
- **Escape every output** at the point of echo (`esc_html`, `esc_attr`, `esc_url`, `esc_textarea`,
  `wp_kses_post`).
- Every state-changing action requires **a nonce** (`wp_verify_nonce` / `check_admin_referer` /
  `check_ajax_referer`) **AND a capability check** (`current_user_can`).
  Nonces are not authentication — always pair them with a capability check.
- All dynamic SQL uses `$wpdb->prepare()` with proper placeholders. Prefer core APIs
  (`WP_Query`, options, meta) over raw SQL.
- **No** `eval`, **no** arbitrary file inclusion from user input, **no** `@` error suppression.
- Enqueue assets via `wp_enqueue_script`/`wp_enqueue_style` with versions; remote resources over HTTPS only.

---

## Definition of Done

A plugin is **done** only when BOTH hold:
1. It **installs and activates** in a real WordPress with **zero PHP fatals**, AND
2. It **passes WordPress Plugin Check with no security errors**.

Additionally, for a fully green run: `php -l` clean, static-structure checks pass, PHPCS has no errors,
PHPStan (level 5, WP stubs) has no errors, the PHPUnit smoke test passes, and header `Version` ==
readme `Stable tag` == any `*_VERSION` constant.

## The gate order (cheap → expensive, fail early)

1. `phpLint` — `php -l` on every `.php` file.
2. `staticStructure` — ABSPATH guard, plugin header, Text Domain == slug, uninstall guard.
3. `phpcs` — WordPress standard incl. security sniffs (errors fail; warnings report).
4. `phpstan` — level 5 with WP stubs.
5. `pluginCheck` — `wp plugin check` inside wp-env; **any `security` error = hard fail.**
6. `activate` — install + activate in wp-env; scan `debug.log` for `PHP Fatal`.
7. `phpunit` — smoke test.
8. `versionConsistency` — header Version == readme Stable tag == `*_VERSION` constant.

Gates 1–4 and 8 run **without Docker**; only 5–7 need wp-env.
