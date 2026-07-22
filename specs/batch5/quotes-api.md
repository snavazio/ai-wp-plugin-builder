# Quotes API

A Quote CPT with an author meta (secure meta box) and a public read-only REST endpoint (GET) returning id, quote (content), and author for published quotes. Public read-only; only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
