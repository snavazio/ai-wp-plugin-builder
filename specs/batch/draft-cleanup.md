# Draft Cleanup

Schedule a weekly WP-Cron event that trashes auto-draft posts older than 7 days and records the count in an option. Register/clear the schedule on activation/deactivation. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
