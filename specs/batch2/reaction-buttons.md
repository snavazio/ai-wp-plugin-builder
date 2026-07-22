# Reaction Buttons

A [reactions] shortcode showing emoji reaction buttons for the current post. Clicks are recorded via AJAX (logged-out allowed) into post meta counters. The handler verifies a nonce, sanitizes input, and returns escaped counts. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
