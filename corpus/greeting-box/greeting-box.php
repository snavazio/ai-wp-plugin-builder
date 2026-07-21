<?php
/**
 * Plugin Name:       Greeting Box
 * Description:       A very small plugin that provides a single shortcode [greeting_box] for the front end.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       greeting-box
 * Domain Path:       /languages
 *
 * @package Gbox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GBOX_VERSION', '1.0.0' );
define( 'GBOX_FILE', __FILE__ );
define( 'GBOX_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function gbox_load_textdomain() {
	load_plugin_textdomain( 'greeting-box', false, dirname( plugin_basename( GBOX_FILE ) ) . '/languages' );
}
add_action( 'init', 'gbox_load_textdomain' );

/**
 * Register the greeting_box shortcode.
 *
 * @return void
 */
function gbox_register_shortcodes() {
	add_shortcode( 'greeting_box', 'gbox_shortcode_greeting_box' );
}
add_action( 'init', 'gbox_register_shortcodes' );

/**
 * Shortcode handler for [greeting_box].
 *
 * @return string
 */
function gbox_shortcode_greeting_box() {
	$message = __( 'Hello, World!', 'greeting-box' );
	return esc_html( $message );
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function gbox_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gbox_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function gbox_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'gbox_deactivate' );
