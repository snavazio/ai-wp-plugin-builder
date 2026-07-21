# Portfolio

A plugin for showcasing agency projects.

- A **Portfolio** custom post type (title + editor + featured image) for individual projects.
- A custom **Project Type** taxonomy (hierarchical) attached to the Portfolio CPT, so projects can be
  categorized (e.g. "Web", "Branding", "Print").
- A `[portfolio]` shortcode that lists recent projects, optionally filtered by a project-type slug
  attribute, rendering an escaped grid.
- An **AJAX "load more"** action (available to logged-out visitors) that returns the next page of
  published projects as escaped HTML. It must verify a nonce, sanitize all input, and only ever return
  published content (no drafts/private).

Clean up posts and terms on uninstall.
