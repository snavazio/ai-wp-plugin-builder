# Workshops API

A Workshop CPT with a date meta (secure meta box) and a public read-only REST endpoint (GET) returning id, title, and date for published workshops. Public read-only; only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
