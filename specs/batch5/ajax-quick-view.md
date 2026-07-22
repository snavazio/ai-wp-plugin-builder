# AJAX Quick View

A [quick_view id=""] shortcode with a button that loads a published post's title and excerpt into a modal via AJAX (logged-out allowed). The handler verifies a nonce, sanitizes the id, and returns escaped content for published posts only. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
