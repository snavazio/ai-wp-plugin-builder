<?php
/**
 * Plugin Name:       Banner CTA Block
 * Description:       A dynamic server-rendered Gutenberg block for full-width banners with heading and button.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       banner-cta-block
 *
 * @package Bctab
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BCTAB_VERSION', '1.0.0' );
define( 'BCTAB_FILE', __FILE__ );
define( 'BCTAB_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function bctab_load_textdomain() {
	load_plugin_textdomain( 'banner-cta-block', false, dirname( plugin_basename( BCTAB_FILE ) ) . '/languages' );
}
add_action( 'init', 'bctab_load_textdomain' );

/**
 * Register the Banner CTA Block.
 *
 * @return void
 */
function bctab_register_banner_cta_block() {
	register_block_type(
		'banner-cta-block/banner-cta',
		array(
			'render_callback' => 'bctab_render_banner_cta_block',
		)
	);
}
add_action( 'init', 'bctab_register_banner_cta_block' );

/**
 * Render the Banner CTA Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function bctab_render_banner_cta_block( $attributes ) {
	// Sanitize attributes.
	$heading     = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
	$button_text = isset( $attributes['button_text'] ) ? sanitize_text_field( $attributes['button_text'] ) : esc_html__( 'Learn More', 'banner-cta-block' );
	$button_url  = isset( $attributes['button_url'] ) ? esc_url_raw( $attributes['button_url'] ) : '#';

	// Escape output.
	$output  = '<div class="bctab-banner-cta">';
	$output .= '<h2 class="bctab-heading">' . esc_html( $heading ) . '</h2>';
	$output .= '<a href="' . esc_url( $button_url ) . '" class="bctab-button">' . esc_html( $button_text ) . '</a>';
	$output .= '</div>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function bctab_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bctab_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function bctab_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'bctab_deactivate' );
