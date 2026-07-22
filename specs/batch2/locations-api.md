# Locations API

A Location CPT with latitude and longitude meta (secure meta box, sanitized) and a public read-only REST endpoint (GET) returning id, title, lat, and lng for published locations. The endpoint is public read-only and must only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
