# AJAX Comment Vote

An upvote button on each comment. Voting via AJAX (logged-out allowed) increments an upvote count in comment meta. The handler verifies a nonce, sanitizes the comment id, and returns the escaped new count. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
