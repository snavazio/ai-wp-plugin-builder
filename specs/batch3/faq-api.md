# FAQ API

An FAQ CPT and a public read-only REST endpoint (GET) returning id, question (title), and answer (content) for published FAQs. Public read-only; only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
