# Announcements API

An Announcement CPT and a public read-only REST endpoint (GET) returning id, title, content, and date for published announcements. The endpoint is public read-only and must only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
