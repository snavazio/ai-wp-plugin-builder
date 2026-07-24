<?php
/**
 * Plugin Name:       Hello Bar
 * Description:       A plugin with a [hello_bar] shortcode that outputs an escaped greeting bar with a configurable name attribute (default World).
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hello-bar
 *
 * @package Hbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HBAR_VERSION', '1.0.0' );
define( 'HBAR_FILE', __FILE__ );
define( 'HBAR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function hbar_load_textdomain() {
	load_plugin_textdomain( 'hello-bar', false, dirname( plugin_basename( HBAR_FILE ) ) . '/languages' );
}
add_action( 'init', 'hbar_load_textdomain' );

/**
 * Register the hello_bar shortcode.
 *
 * @return void
 */
function hbar_register_shortcodes() {
	add_shortcode( 'hello_bar', 'hbar_shortcode_hello_bar' );
}
add_action( 'init', 'hbar_register_shortcodes' );

/**
 * Shortcode handler for [hello_bar].
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function hbar_shortcode_hello_bar( $atts ) {
	$defaults = array(
		'name' => __( 'World', 'hello-bar' ),
	);
	$atts     = shortcode_atts( $defaults, $atts, 'hello_bar' );

	// Sanitize input.
	$name = sanitize_text_field( $atts['name'] );

	// Escape output.
	$output = '<div class="hello-bar">' . esc_html__( 'Hello, ', 'hello-bar' ) . esc_html( $name ) . esc_html__( '!', 'hello-bar' ) . '</div>';
	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function hbar_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'hbar_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function hbar_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'hbar_deactivate' );
