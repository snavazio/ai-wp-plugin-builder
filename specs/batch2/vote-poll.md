# Vote Poll

A [poll] shortcode showing a yes/no question and two buttons. Clicking submits a vote via AJAX (logged-out allowed) that increments per-option counts stored in an option. The handler verifies a nonce, sanitizes input, and returns the escaped updated tallies. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
