# Job Board

A Job CPT with location and salary meta (secure meta box), a hierarchical Category taxonomy, an admin Location column, a [jobs category=""] shortcode, and a public read-only REST endpoint (GET) returning published jobs' id, title, and location. Public read-only; only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check. Remove data and terms on uninstall.
