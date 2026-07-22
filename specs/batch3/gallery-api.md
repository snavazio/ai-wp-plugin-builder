# Gallery API

A Photo CPT with a caption meta (secure meta box) and a public read-only REST endpoint (GET) returning id, title, permalink, and caption for published photos. Public read-only; only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
