# Transient Cleanup

On activation, schedule a daily WP-Cron event that deletes this plugin's own expired option-stored cache entries; log the last run time to an option. Clear the schedule on deactivation. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
