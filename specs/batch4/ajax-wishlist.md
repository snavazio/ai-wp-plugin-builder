# AJAX Wishlist

A [wishlist_button] shortcode adding the current post to a per-user wishlist via AJAX (logged-out allowed, stored in a cookie-keyed option). The handler verifies a nonce, sanitizes the post id, and returns the escaped wishlist count. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
