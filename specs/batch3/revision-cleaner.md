# Revision Cleaner

Schedule a weekly WP-Cron event that deletes post revisions older than 60 days, keeping the 5 most recent per post, and records the deleted count in an option. Register/clear on activation/deactivation. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
