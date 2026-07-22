# AJAX Newsletter

A [newsletter] shortcode with an email field submitted via AJAX (logged-out allowed). The handler verifies a nonce, sanitizes the email (is_email/sanitize_email), stores it in an option list (deduped), and returns an escaped confirmation. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
