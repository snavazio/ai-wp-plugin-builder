# Artworks API

An Artwork CPT with an artist meta (secure meta box) and a public read-only REST endpoint (GET) returning id, title, and artist for published artworks. Public read-only; only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
