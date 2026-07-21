# Events REST

A small plugin that registers an **Events** custom post type and exposes a **read-only REST API**
endpoint listing published events.

Events CPT:
- standard title + content (editor),
- an **event date** custom field (store as a sanitized date string),
- an **event location** custom field (text).
- Show these fields in a secure meta box on the edit screen (nonce + capability + sanitize).

REST endpoint:
- namespace like `events/v1`, route `/events`,
- method **GET only**, publicly readable (it only returns already-published content),
- returns a JSON array of published events with: id, title, permalink, event date, and location.
- All output values escaped/prepared appropriately; the endpoint must not expose drafts or private data.

Clean up event posts/meta on uninstall.
