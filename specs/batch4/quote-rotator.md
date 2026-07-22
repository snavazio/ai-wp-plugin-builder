# Daily Quote Rotator

Schedule a daily WP-Cron event that picks a random quote from a stored list and sets it as the "quote of the day" option. A [quote_of_day] shortcode renders it, escaped. Register/clear on activation/deactivation. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
