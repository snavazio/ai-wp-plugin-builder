# Testimonials API

A Testimonial CPT with an author meta field (secure meta box) and a public read-only REST endpoint (namespace tstm/v1, route /testimonials, GET) returning id, title, content and author for published testimonials only, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
