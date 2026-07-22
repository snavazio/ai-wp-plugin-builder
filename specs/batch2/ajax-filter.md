# AJAX Post Filter

A [post_filter] shortcode with category buttons that fetch matching published posts via AJAX (logged-out allowed). The handler verifies a nonce, sanitizes the category, and returns only published posts as escaped HTML. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
