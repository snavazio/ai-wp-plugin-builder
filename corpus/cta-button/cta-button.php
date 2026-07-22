<?php
/**
 * Plugin Name:       CTA Button
 * Description:       A shortcode for rendering a styled call-to-action button with sanitized URL and escaped text.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cta-button
 *
 * @package Ctabtn
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CTABTN_VERSION', '1.0.0' );
define( 'CTABTN_FILE', __FILE__ );
define( 'CTABTN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ctabtn_load_textdomain() {
	load_plugin_textdomain( 'cta-button', false, dirname( plugin_basename( CTABTN_FILE ) ) . '/languages' );
}
add_action( 'init', 'ctabtn_load_textdomain' );

/**
 * Register the cta_button shortcode.
 *
 * @return void
 */
function ctabtn_register_shortcodes() {
	add_shortcode( 'cta_button', 'ctabtn_shortcode_cta_button' );
}
add_action( 'init', 'ctabtn_register_shortcodes' );

/**
 * Shortcode handler for [cta_button].
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function ctabtn_shortcode_cta_button( $atts ) {
	$defaults = array(
		'text' => __( 'Call to Action', 'cta-button' ),
		'url'  => '#',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'cta_button' );

	// Sanitize input.
	$text = sanitize_text_field( $atts['text'] );
	$url  = esc_url( $atts['url'] );

	// Escape output.
	$output = '<a href="' . esc_url( $url ) . '" class="cta-button">' . esc_html( $text ) . '</a>';
	return $output;
}
