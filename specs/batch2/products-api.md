# Products API

A Product CPT with a price meta field (secure meta box) and a public read-only REST endpoint (GET) returning id, title, permalink, and price for published products. The endpoint is public read-only and must only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
