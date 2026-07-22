/**
 * Canonical fix knowledge-base. Maps recurring gate-error signatures to a precise, actionable fix. On a
 * fix turn we scan the failing errors and inject only the matching canonical fixes, so a smaller local
 * model gets the exact remedy for the nitpicky WPCS / Plugin Check rules it repeatedly trips on
 * (measured: inline-comment punctuation, i18n placeholders, escaping, nonce/cap, global overrides).
 */
interface FixRule {
  match: RegExp;
  fix: string;
}

const RULES: FixRule[] = [
  {
    match: /InlineComment\.InvalidEndChar|Inline comments must end/i,
    fix: 'End every inline `//` comment with a period, exclamation mark, or question mark.',
  },
  {
    match: /EscapeOutput\.OutputNotEscaped|output should be run through an escaping/i,
    fix: 'Escape at the point of echo: esc_html()/esc_attr()/esc_url()/esc_textarea()/wp_kses_post().',
  },
  {
    match: /MissingUnslash|not unslashed/i,
    fix: 'Call wp_unslash() on the superglobal value BEFORE sanitizing it.',
  },
  {
    match: /InputNotSanitized|non-sanitized input/i,
    fix: 'Sanitize the input with a type-appropriate function: sanitize_text_field(), absint(), sanitize_email(), esc_url_raw(), sanitize_key().',
  },
  {
    match: /InputNotValidated|possibly undefined superglobal/i,
    fix: 'Guard the superglobal with isset() before reading it (e.g. isset( $_POST[\'x\'] ) ).',
  },
  {
    match: /NonceVerification|without nonce verification/i,
    fix: 'Before any state change, verify a nonce (check_admin_referer()/wp_verify_nonce()/check_ajax_referer()) AND check current_user_can( <cap> ). Both are required.',
  },
  {
    match: /PreparedSQL|InterpolatedNotPrepared|placeholders and \$wpdb->prepare/i,
    fix: 'Use $wpdb->prepare() with %s/%d/%f placeholders; never interpolate a variable into the SQL string.',
  },
  {
    match: /GlobalVariablesOverride|Overriding WordPress globals/i,
    fix: 'Rename the variable to a prefixed name. In global-scope files (e.g. uninstall.php) never assign to bare names like $post_id/$post/$id — use $prefix_post_id.',
  },
  {
    match: /PrefixAllGlobals|should start with the theme\/plugin prefix|NonPrefixed/i,
    fix: 'Prefix every global function, class, constant, option key, hook, and global-scope variable with the plugin prefix (never wp_/__/_).',
  },
  {
    match: /WP\.I18n|placeholders was|I18n\./i,
    fix: 'For translatable strings with dynamic values, wrap the literal in __() and pass it through sprintf()/printf(); add a "// translators:" comment describing each placeholder. Never put a variable inside the __() text.',
  },
  {
    match: /Squiz\.Commenting|Missing (parameter|@param|@return|function) comment|MissingParamTag|doc comment/i,
    fix: 'Add a docblock above the function/class with a short description and @param/@return tags.',
  },
  {
    match: /Yoda/i,
    fix: 'Use Yoda conditions: put the constant/literal on the left of the comparison (e.g. if ( \'x\' === $var )).',
  },
  {
    match: /missing_direct_file_access_protection|prevent direct access/i,
    fix: "Add `if ( ! defined( 'ABSPATH' ) ) { exit; }` at the very top of the file (below the header docblock).",
  },
  {
    match: /load_plugin_textdomain.*discouraged/i,
    fix: 'This is only a warning; leave load_plugin_textdomain as-is unless the gate hard-fails on it.',
  },
  {
    match: /Stable tag|Version.*!=|version consistency/i,
    fix: 'Make the plugin header Version, the readme.txt "Stable tag", and every *_VERSION constant identical.',
  },
  {
    match: /PHPUnit failed|Tests:.*Failures|Failed asserting|assertTrue|assertSame/i,
    fix:
      'PHPUnit assertTrue() is STRICT: assertTrue(10) FAILS because 10 !== true. has_action()/has_filter() ' +
      'return an int priority (often 10) or false — assert them with assertNotFalse(), NEVER assertTrue(). ' +
      'Use assertTrue() ONLY for real booleans: post_type_exists(), taxonomy_exists(), shortcode_exists(), ' +
      'is_*(), defined(). Keep smoke assertions to registration/wiring and a shortcode\'s graceful empty-state; ' +
      'do NOT assert rendered markup or option values that need fixtures.',
  },
];

/** Return the unique canonical fixes relevant to the given gate errors. */
export function matchFixes(errors: string[]): string[] {
  const hits = new Set<string>();
  for (const e of errors) {
    for (const r of RULES) {
      if (r.match.test(e)) hits.add(r.fix);
    }
  }
  return [...hits];
}

/** Format matched fixes as a compact checklist for the fix prompt. */
export function formatFixes(errors: string[]): string {
  const fixes = matchFixes(errors);
  if (fixes.length === 0) return '';
  return 'CANONICAL FIXES for these error types (apply exactly):\n' + fixes.map((f) => `- ${f}`).join('\n');
}
