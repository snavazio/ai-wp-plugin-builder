<?php
/**
 * Plugin Name:       Color Note
 * Description:       Adds a [color_note] shortcode for creating colored note boxes.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       color-note
 *
 * @package Cnote
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CNOTE_VERSION', '1.0.0' );
define( 'CNOTE_FILE', __FILE__ );
define( 'CNOTE_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cnote_load_textdomain() {
	load_plugin_textdomain( 'color-note', false, dirname( plugin_basename( CNOTE_FILE ) ) . '/languages' );
}
add_action( 'init', 'cnote_load_textdomain' );

/**
 * Register the color_note shortcode.
 *
 * @return void
 */
function cnote_register_shortcodes() {
	add_shortcode( 'color_note', 'cnote_shortcode_color_note' );
}
add_action( 'init', 'cnote_register_shortcodes' );

/**
 * Shortcode handler for [color_note].
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content The content of the shortcode.
 * @return string
 */
function cnote_shortcode_color_note( $atts, $content = null ) {
	$defaults = array(
		'color' => 'blue',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'color_note' );

	// Sanitize color attribute using whitelist.
	$allowed_colors = array( 'blue', 'green', 'red', 'yellow', 'purple', 'orange', 'gray', 'black', 'white' );
	$color          = in_array( $atts['color'], $allowed_colors ) ? $atts['color'] : 'blue';

	// Escape content for safe output.
	$content = esc_html( $content );

	// Escape color for attribute safety.
	$output = '<div class="color-note color-note-' . esc_attr( $color ) . '">' . $content . '</div>';
	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cnote_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cnote_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cnote_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cnote_deactivate' );
