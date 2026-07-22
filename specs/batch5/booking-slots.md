# Booking Slots

A Slot CPT with a datetime meta (secure meta box), a [slots] shortcode listing available published slots each with a "book" button, and an AJAX handler (logged-out allowed) that marks a slot booked in post meta after verifying a nonce and sanitizing input, returning an escaped result. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
