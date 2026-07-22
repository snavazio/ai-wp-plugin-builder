<?php
/**
 * Plugin Name:       Stat Counter Block
 * Description:       A dynamic server-rendered Gutenberg block displaying a statistic with a label.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       stat-counter-block
 *
 * @package Scblock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SCBLOCK_VERSION', '1.0.0' );
define( 'SCBLOCK_FILE', __FILE__ );
define( 'SCBLOCK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function scblock_load_textdomain() {
	load_plugin_textdomain( 'stat-counter-block', false, dirname( plugin_basename( SCBLOCK_FILE ) ) . '/languages' );
}
add_action( 'init', 'scblock_load_textdomain' );

/**
 * Register the Stat Counter Block.
 *
 * @return void
 */
function scblock_register_stat_counter_block() {
	register_block_type(
		'scblock/stat-counter',
		array(
			'render_callback' => 'scblock_render_stat_counter_block',
		)
	);
}
add_action( 'init', 'scblock_register_stat_counter_block' );

/**
 * Render the Stat Counter Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function scblock_render_stat_counter_block( $attributes ) {
	// Sanitize attributes.
	$stat  = isset( $attributes['stat'] ) ? sanitize_text_field( $attributes['stat'] ) : '';
	$label = isset( $attributes['label'] ) ? sanitize_text_field( $attributes['label'] ) : '';

	// Escape output.
	$output  = '<div class="scblock-stat-counter">';
	$output .= '<span class="scblock-stat">' . esc_html( $stat ) . '</span>';
	$output .= '<span class="scblock-label">' . esc_html( $label ) . '</span>';
	$output .= '</div>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function scblock_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'scblock_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function scblock_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'scblock_deactivate' );
