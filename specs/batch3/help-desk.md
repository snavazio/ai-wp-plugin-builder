# Help Desk

A Ticket CPT with requester-email and priority meta (secure meta box), a hierarchical Status taxonomy (Open/Pending/Closed), an admin Status column, a [tickets status=""] shortcode listing tickets by status, and a public read-only REST endpoint (GET) returning published tickets' id, title, and status. Public read-only; only ever return already-published content (no drafts/private). Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check. Remove data and terms on uninstall.
