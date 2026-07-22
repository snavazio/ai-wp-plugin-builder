<?php
/**
 * Plugin Name:       Rating Stars Block
 * Description:       A dynamic server-rendered Gutenberg block rendering a 1-5 star rating from a value attribute (clamped).
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rating-stars-block
 *
 * @package Rsblock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RSBLOCK_VERSION', '1.0.0' );
define( 'RSBLOCK_FILE', __FILE__ );
define( 'RSBLOCK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rsblock_load_textdomain() {
	load_plugin_textdomain( 'rating-stars-block', false, dirname( plugin_basename( RSBLOCK_FILE ) ) . '/languages' );
}
add_action( 'init', 'rsblock_load_textdomain' );

/**
 * Register the Rating Stars Block.
 *
 * @return void
 */
function rsblock_register_rating_stars_block() {
	register_block_type(
		'rating-stars-block/rating-stars',
		array(
			'render_callback' => 'rsblock_render_rating_stars_block',
		)
	);
}
add_action( 'init', 'rsblock_register_rating_stars_block' );

/**
 * Render the Rating Stars Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function rsblock_render_rating_stars_block( $attributes ) {
	// Sanitize the value attribute.
	$value = isset( $attributes['value'] ) ? absint( $attributes['value'] ) : 3;
	$value = max( 1, min( 5, $value ) );

	// translators: %d is the number of stars.
	$aria_label = _n( '%d star', '%d stars', $value, 'rating-stars-block' );
	$aria_label = sprintf( $aria_label, $value );
	$aria_label = esc_attr( $aria_label );

	// Start building the output.
	$output = '<div class="rsblock-rating-stars" aria-label="' . $aria_label . '">';

	// Loop for 5 stars.
	for ( $i = 1; $i <= 5; $i++ ) {
		$class   = $i <= $value ? 'rsblock-star-filled' : '';
		$output .= '<span class="rsblock-star ' . esc_attr( $class ) . '">' . esc_html( '&#9733;' ) . '</span>';
	}

	$output .= '</div>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function rsblock_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rsblock_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rsblock_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rsblock_deactivate' );
