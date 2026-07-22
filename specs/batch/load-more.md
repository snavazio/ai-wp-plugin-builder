# Load More Posts

A [load_more] shortcode listing recent published posts with a button that loads the next page via AJAX (available logged-out). The AJAX handler verifies a nonce, sanitizes the page number, and returns only published posts as escaped HTML. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
