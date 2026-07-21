=== Client Notes (Bad Sample) ===
Contributors: stephennavazio
Tags: crm, notes
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The known-INSECURE sample. Do NOT ship. Exists only to prove the harness rejects insecure code.

== Description ==

Same feature set as the good sample, but with four planted vulnerabilities: a missing ABSPATH guard,
an unescaped echo of $_GET, a $wpdb->query with string interpolation, and a form handler with no
nonce/capability check.

== Changelog ==

= 1.0.0 =
* Intentionally insecure sample.
