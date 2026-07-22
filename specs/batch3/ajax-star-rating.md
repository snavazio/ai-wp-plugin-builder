# AJAX Star Rating

A [star_rating] shortcode showing 1-5 stars for the current post. Submitting a rating via AJAX (logged-out allowed) updates a running average in post meta. The handler verifies a nonce, clamps the rating 1-5, and returns the escaped new average. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
