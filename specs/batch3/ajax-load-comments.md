# AJAX Load Comments

A [load_comments] shortcode that fetches the next page of approved comments for the current post via AJAX (logged-out allowed). The handler verifies a nonce, sanitizes input, and returns only approved comments as escaped HTML. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
