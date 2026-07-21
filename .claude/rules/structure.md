# Rule: Plugin Structure

## Main plugin file header
The main file (`<slug>.php`) MUST contain a header block with at least:

```php
/**
 * Plugin Name:       Human Readable Name
 * Description:       One sentence.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       <slug>
 * Domain Path:       /languages
 */
```

- `Text Domain` MUST equal the plugin folder slug exactly.
- `Version` MUST match `readme.txt` `Stable tag` and any `*_VERSION` constant.

## Folder / packaging
- The `.zip` contains exactly one root folder named exactly `<slug>`.
- No dev files in the zip (use `.distignore`): no `node_modules`, `vendor` (unless runtime), tests, `.git`, `composer.*`, `phpcs.xml`, etc.

## Prefixing
- Every global function, class, constant, and option key is prefixed with a unique 4–5+ char prefix
  (e.g. `cnote_`, `CNOTE_`, `Cnote_`).
- NEVER prefix with `wp_`, `__`, or `_` (reserved by WordPress core).

## Lifecycle
- Use `register_activation_hook( __FILE__, ... )` and `register_deactivation_hook( __FILE__, ... )`.
- Flush rewrite rules on activation/deactivation only if the plugin registers CPTs / rewrite rules.
- `uninstall.php` MUST start with:
  ```php
  if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
      die;
  }
  ```

## Internationalization
- Load text domain (`load_plugin_textdomain`) on `init` if translations are shipped.
- Every user-facing string wrapped in `__()`, `esc_html__()`, `esc_attr__()`, `_e()`, `esc_html_e()`, etc.
- The text domain argument is ALWAYS a string literal matching the slug — never a variable or constant.

## readme.txt
- Valid WordPress `readme.txt` with `Stable tag:` line equal to header `Version`.
- Include `Requires at least`, `Tested up to`, `Requires PHP`.
