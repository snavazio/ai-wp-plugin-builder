<?php
/**
 * Plugin Name:       Tiny Hello
 * Description:       A shortcode that outputs an escaped hello with a name attribute.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tiny-hello
 *
 * @package Thl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'THL_VERSION', '1.0.0' );
define( 'THL_FILE', __FILE__ );
define( 'THL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function thl_load_textdomain() {
	load_plugin_textdomain( 'tiny-hello', false, dirname( plugin_basename( THL_FILE ) ) . '/languages' );
}
add_action( 'init', 'thl_load_textdomain' );

/**
 * Register the tiny_hello shortcode.
 *
 * @return void
 */
function thl_register_shortcodes() {
	add_shortcode( 'tiny_hello', 'thl_shortcode_tiny_hello' );
}
add_action( 'init', 'thl_register_shortcodes' );

/**
 * Shortcode handler for [tiny_hello].
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function thl_shortcode_tiny_hello( $atts ) {
	$defaults = array(
		'name' => __( 'World', 'tiny-hello' ),
	);
	$atts     = shortcode_atts( $defaults, $atts, 'tiny_hello' );

	// Sanitize input.
	$name = sanitize_text_field( $atts['name'] );

	// Escape output.
	$output = '<div class="tiny-hello">' . esc_html__( 'Hello, ', 'tiny-hello' ) . esc_html( $name ) . esc_html__( '!', 'tiny-hello' ) . '</div>';
	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function thl_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'thl_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function thl_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'thl_deactivate' );
