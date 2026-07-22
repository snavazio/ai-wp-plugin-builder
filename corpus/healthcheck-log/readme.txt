=== Healthcheck Log ===
Contributors: stephennavazio
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Schedule a daily WP-Cron event that records a heartbeat timestamp and the current published-post count into an option (keep the last 14). Register/clear on activation/deactivation. A [healthcheck] shortcode shows the latest entry, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.

== Description ==

Schedule a daily WP-Cron event that records a heartbeat timestamp and the current published-post count into an option (keep the last 14). Register/clear on activation/deactivation. A [healthcheck] shortcode shows the latest entry, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate through the Plugins screen.

== Changelog ==

= 1.0.0 =
* Initial release.
