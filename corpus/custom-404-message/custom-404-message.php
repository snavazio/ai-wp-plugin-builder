<?php
/**
 * Plugin Name:       Custom 404 Message
 * Description:       A settings page with a custom 404 message (basic HTML allowed, sanitized); render the escaped message on 404 pages via the template flow. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-404-message
 *
 * @package C404m
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'C404M_VERSION', '1.0.0' );
define( 'C404M_FILE', __FILE__ );
define( 'C404M_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function c404m_load_textdomain() {
	load_plugin_textdomain( 'custom-404-message', false, dirname( plugin_basename( C404M_FILE ) ) . '/languages' );
}
add_action( 'init', 'c404m_load_textdomain' );

// Include feature files.
require_once C404M_PATH . 'includes/admin-page.php';

/**
 * Render the custom 404 message on 404 pages.
 *
 * @param string $content The current content.
 * @return string The modified content.
 */
function c404m_render_404( $content ) {
	if ( is_404() && ! is_admin() ) {
		$message = get_option( 'c404m_message', '' );
		// Escape message for safe output using wp_kses_post (allows basic HTML).
		return wp_kses_post( $message );
	}
	return $content;
}
add_filter( 'the_content', 'c404m_render_404', 10, 1 );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function c404m_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'c404m_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function c404m_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'c404m_deactivate' );
