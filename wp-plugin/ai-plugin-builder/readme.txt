=== AI Plugin Builder ===
Contributors: stephennavazio
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Admin screen to generate security-audited WordPress plugins from a plain-English spec.

== Description ==

The companion WordPress plugin for the AI WP Plugin Builder. From an admin screen you describe a plugin in
plain English, pick an engine (Claude or a local Ollama model), and the builder service generates it, runs
it through an 8-gate security/quality harness, and returns an install-ready .zip you can download or install
in one click.

This plugin is a thin, secure client: it talks to the builder **service** (a separate process running
`npm run serve` from the AI WP Plugin Builder tool) server-side, so the API key never reaches the browser.
All generation and verification happens on the service, not inside WordPress.

== Installation ==

1. Install and activate this plugin.
2. Run the builder service on a machine with Docker + Node + PHP: `AIWPB_API_KEY=<secret> npm run serve`.
3. In WordPress: AI Plugin Builder → Settings, enter the service URL and the same API key.
4. Open AI Plugin Builder, type a spec, choose an engine, and click Generate.

== Changelog ==

= 1.0.0 =
* Initial release: spec-to-plugin admin screen with live progress, download, and one-click install.
