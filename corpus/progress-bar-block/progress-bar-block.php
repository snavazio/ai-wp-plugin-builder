<?php
/**
 * Plugin Name:       Progress Bar Block
 * Description:       A dynamic server-rendered Gutenberg block rendering a labelled progress bar from a percent attribute (clamped 0-100), escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       progress-bar-block
 *
 * @package Pbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PBAR_VERSION', '1.0.0' );
define( 'PBAR_FILE', __FILE__ );
define( 'PBAR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function pbar_load_textdomain() {
	load_plugin_textdomain( 'progress-bar-block', false, dirname( plugin_basename( PBAR_FILE ) ) . '/languages' );
}
add_action( 'init', 'pbar_load_textdomain' );

/**
 * Register the Progress Bar Block.
 *
 * @return void
 */
function pbar_register_progress_bar_block() {
	register_block_type(
		'progress-bar-block/progress-bar',
		array(
			'render_callback' => 'pbar_render_progress_bar_block',
		)
	);
}
add_action( 'init', 'pbar_register_progress_bar_block' );

/**
 * Render the Progress Bar Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function pbar_render_progress_bar_block( $attributes ) {
	// Sanitize percent attribute (integer clamped 0-100).
	$percent = isset( $attributes['percent'] ) ? absint( $attributes['percent'] ) : 0;
	$percent = max( 0, min( 100, $percent ) );

	// Escape all output.
	$output  = '<div class="pbar-progress-bar">';
	$output .= '<div class="pbar-progress-label">' . esc_html__( 'Progress', 'progress-bar-block' ) . '</div>';
	$output .= '<div class="pbar-progress-container">';
	$output .= '<div class="pbar-progress-bar" style="width: ' . esc_attr( (string) $percent ) . '%"></div>';
	$output .= '</div>';
	$output .= '<div class="pbar-progress-value">' . esc_html( (string) $percent ) . '%</div>';
	$output .= '</div>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function pbar_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pbar_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function pbar_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pbar_deactivate' );
