# AJAX Availability Check

A [availability] shortcode with a date field that checks availability via AJAX (logged-out allowed) against a stored option of booked dates. The handler verifies a nonce, sanitizes the date, and returns an escaped available/unavailable result. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
