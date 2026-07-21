# Rule: Security (every item is a hard gate)

## 1. Direct-access guard
Every executable PHP file (anything that defines/runs code when loaded) starts with:
```php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
```
Exception: `uninstall.php` uses the `WP_UNINSTALL_PLUGIN` guard instead.

## 2. Input sanitization
- Treat ALL of `$_GET`, `$_POST`, `$_REQUEST`, `$_COOKIE`, `$_SERVER` as untrusted.
- `wp_unslash()` before sanitizing.
- Sanitize with the type-appropriate function:
  - text → `sanitize_text_field()`
  - integer → `absint()` / `intval()`
  - email → `sanitize_email()`
  - URL (store) → `esc_url_raw()`
  - key/slug → `sanitize_key()`, `sanitize_title()`
  - textarea → `sanitize_textarea_field()`
  - HTML → `wp_kses()` / `wp_kses_post()`
- Never trust a value just because it came through a nonce-protected form.

## 3. Output escaping (escape LATE, at the echo)
- `esc_html()` for text, `esc_attr()` for attributes, `esc_url()` for URLs,
  `esc_textarea()` for textarea contents, `wp_kses_post()` for rich HTML.
- Use the `_e` / `esc_html_e` i18n variants for translated echoes.
- Never `echo` a superglobal or DB value without escaping.

## 4. Nonces + capabilities (BOTH, always, for state changes)
- Admin form: `wp_nonce_field()` on render, `check_admin_referer()` on handle.
- AJAX: `check_ajax_referer()`.
- REST: `permission_callback` that checks `current_user_can()` (never return `true` for write routes).
- ALWAYS pair the nonce with `current_user_can( '<capability>' )`. A nonce proves intent, not authority.

## 5. Database
- All dynamic SQL through `$wpdb->prepare()` with `%s` / `%d` / `%f` placeholders.
- Never interpolate a variable directly into a query string.
- Prefer core APIs: `WP_Query`, `get_option`/`update_option`, `get_post_meta`/`update_post_meta`.
- Table names via `$wpdb->prefix` / `$wpdb->posts` etc.

## 6. Dangerous constructs — forbidden
- No `eval()`.
- No including/requiring a path derived from user input.
- No `@` error suppression.
- No `extract()` on request data.
- No `unserialize()` of untrusted input.

## 7. Assets & remote resources
- Register/enqueue scripts and styles via `wp_enqueue_script` / `wp_enqueue_style` with a version arg.
- Any remote URL loaded is HTTPS only.
- Use `wp_remote_get` / `wp_remote_post` (not raw cURL/`file_get_contents` on URLs) and check for `WP_Error`.
