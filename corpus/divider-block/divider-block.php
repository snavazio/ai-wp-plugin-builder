<?php
/**
 * Plugin Name:       Divider Block
 * Description:       A dynamic server-rendered Gutenberg block for styled dividers with solid, dashed, or dotted styles.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       divider-block
 *
 * @package Divb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DIVB_VERSION', '1.0.0' );
define( 'DIVB_FILE', __FILE__ );
define( 'DIVB_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function divb_load_textdomain() {
	load_plugin_textdomain( 'divider-block', false, dirname( plugin_basename( DIVB_FILE ) ) . '/languages' );
}
add_action( 'init', 'divb_load_textdomain' );

/**
 * Register the Divider Block.
 *
 * @return void
 */
function divb_register_divider_block() {
	register_block_type(
		'divb/divider-block',
		array(
			'render_callback' => 'divb_render_divider_block',
		)
	);
}
add_action( 'init', 'divb_register_divider_block' );

/**
 * Render the Divider Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function divb_render_divider_block( $attributes ) {
	// Sanitize style attribute.
	$style = isset( $attributes['style'] ) ? sanitize_text_field( $attributes['style'] ) : 'solid';
	if ( ! in_array( $style, array( 'solid', 'dashed', 'dotted' ), true ) ) {
		$style = 'solid';
	}

	// Escape output.
	$output  = '<hr class="divider-block-divider divider-block-';
	$output .= esc_attr( $style );
	$output .= '" />';
	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function divb_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'divb_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function divb_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'divb_deactivate' );
