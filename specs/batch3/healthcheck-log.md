# Healthcheck Log

Schedule a daily WP-Cron event that records a heartbeat timestamp and the current published-post count into an option (keep the last 14). Register/clear on activation/deactivation. A [healthcheck] shortcode shows the latest entry, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
