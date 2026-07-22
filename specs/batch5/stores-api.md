# Stores API

A Store CPT with address and hours meta (secure meta box) and a public read-only REST endpoint (GET) returning id, title, address, and hours for published stores. Public read-only; only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
