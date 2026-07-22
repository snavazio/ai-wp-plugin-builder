# Team Directory

A Team Member CPT with role and email meta (secure meta box), a hierarchical Department taxonomy, an admin Department column, a [team department=""] shortcode, and a public read-only REST endpoint (GET) returning published members. The endpoint is public read-only and must only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check. Remove data and terms on uninstall.
