# Post Likes

An AJAX "like" button for posts, available to logged-out visitors. Clicking increments a per-post like counter stored in post meta. The handler must verify a nonce, sanitize the post id, and return the new escaped count. A [post_likes] shortcode renders the button and current count. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
