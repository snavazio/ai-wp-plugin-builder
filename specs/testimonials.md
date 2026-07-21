# Testimonials

I want a plugin that adds a **Testimonials** custom post type for client testimonials.

Each testimonial should have:
- the testimonial text (use the normal editor / title),
- a **star rating** from 1 to 5,
- the **customer name**,
- the **customer company** (optional).

In the WordPress admin, the Testimonials list table should show an extra **Rating** column
displaying the star rating for each testimonial, so staff can scan them quickly.

The custom fields should be editable from a meta box on the testimonial edit screen. Saving the
meta box must be secure (nonce + capability check), inputs sanitized, and the rating clamped to 1–5.

Keep it private/admin-facing (the CPT doesn't need a public archive). Clean up its data on uninstall.
