# AJAX Contact Form

A [contact_form] shortcode with name/email/message fields submitted via AJAX (logged-out allowed). The handler verifies a nonce, sanitizes each field (sanitize_text_field/sanitize_email), stores the submission as a private Submission CPT post, and returns an escaped success/error message. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
