<?php
/**
 * Plugin Name:       Hero Block
 * Description:       Dynamic server-rendered Gutenberg block for hero sections with heading, subheading, and button.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hero-block
 *
 * @package Hblock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HBLOCK_VERSION', '1.0.0' );
define( 'HBLOCK_FILE', __FILE__ );
define( 'HBLOCK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function hblock_load_textdomain() {
	load_plugin_textdomain( 'hero-block', false, dirname( plugin_basename( HBLOCK_FILE ) ) . '/languages' );
}
add_action( 'init', 'hblock_load_textdomain' );

/**
 * Register the Hero Block.
 *
 * @return void
 */
function hblock_register_hero_block() {
	register_block_type(
		'hero-block/hero',
		array(
			'render_callback' => 'hblock_render_hero_block',
		)
	);
}
add_action( 'init', 'hblock_register_hero_block' );

/**
 * Render the Hero Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function hblock_render_hero_block( $attributes ) {
	// Sanitize attributes.
	$heading     = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
	$subheading  = isset( $attributes['subheading'] ) ? sanitize_text_field( $attributes['subheading'] ) : '';
	$button_text = isset( $attributes['buttonText'] ) ? sanitize_text_field( $attributes['buttonText'] ) : '';
	$button_url  = isset( $attributes['buttonUrl'] ) ? esc_url_raw( $attributes['buttonUrl'] ) : '';

	// Escape output.
	$output  = '<div class="hblock-hero">';
	$output .= '<h1 class="hblock-heading">' . esc_html( $heading ) . '</h1>';
	$output .= '<p class="hblock-subheading">' . esc_html( $subheading ) . '</p>';
	$output .= '<a href="' . esc_url( $button_url ) . '" class="hblock-button">' . esc_html( $button_text ) . '</a>';
	$output .= '</div>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function hblock_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'hblock_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function hblock_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'hblock_deactivate' );
