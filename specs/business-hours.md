# Business Hours

A plugin that lets the site owner enter their **business opening hours** on a settings page, then
display them on the front end with a shortcode.

Settings page (under the Settings menu):
- one row per day of the week (Monday–Sunday),
- for each day: an "open" time and a "close" time as free text (e.g. "9:00 AM" / "5:00 PM"),
  or the ability to mark the day as **Closed**.
- Saving the settings must be secure (settings API or nonce + capability check) and sanitized.

Front end:
- a shortcode `[business_hours]` that renders the week's hours as a clean, escaped HTML table/list,
  reading from the saved settings. Days marked closed should show "Closed".

Remove the saved option on uninstall.
