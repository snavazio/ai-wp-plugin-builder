# Stats Snapshot

Schedule a daily WP-Cron event that stores a snapshot of published post and comment counts into an option (keeping the latest 30). Register/clear on activation/deactivation. A [stats_snapshot] shortcode shows the latest snapshot, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
