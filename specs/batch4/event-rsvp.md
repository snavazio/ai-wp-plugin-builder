# Event RSVP

An Event CPT with a date meta (secure meta box), an [event_rsvp id=""] shortcode showing an RSVP button, and an AJAX handler (logged-out allowed) that increments an RSVP count in post meta after verifying a nonce and sanitizing input, returning the escaped count. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
