# AJAX Tabs

A [tabs ids="1,2,3"] shortcode rendering tab buttons; clicking a tab loads that published post's excerpt via AJAX (logged-out allowed). The handler verifies a nonce, sanitizes the id, and returns escaped content for published posts only. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
