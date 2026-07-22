# Header Code

A settings page with a textarea for custom <head> markup (e.g. analytics/meta tags). Output it in wp_head. Sanitize on save and restrict to admins. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
