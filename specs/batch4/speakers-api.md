# Speakers API

A Speaker CPT with a title meta (secure meta box) and a public read-only REST endpoint (GET) returning id, name (title), and title (role) for published speakers. Public read-only; only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
