# Quick Search

A [quick_search] shortcode with an input that fetches title suggestions via AJAX (logged-out allowed). The handler verifies a nonce, sanitizes the query, searches published posts, and returns escaped titles/links only. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
