# Link Audit Log

Schedule a daily WP-Cron event that counts published posts containing external links and stores the tally with a timestamp in an option (keep last 14). Register/clear on activation/deactivation. A [link_audit] shortcode shows the latest, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
