<?php
/**
 * Plugin Name:       Pull Quote Block
 * Description:       A dynamic server-rendered Gutenberg block for styled pull quotes with quote and citation attributes.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pull-quote-block
 *
 * @package Pqbl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PQBL_VERSION', '1.0.0' );
define( 'PQBL_FILE', __FILE__ );
define( 'PQBL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function pqbl_load_textdomain() {
	load_plugin_textdomain( 'pull-quote-block', false, dirname( plugin_basename( PQBL_FILE ) ) . '/languages' );
}
add_action( 'init', 'pqbl_load_textdomain' );

/**
 * Register the Pull Quote Block.
 *
 * @return void
 */
function pqbl_register_pull_quote_block() {
	register_block_type(
		'pull-quote-block/pull-quote',
		array(
			'render_callback' => 'pqbl_render_pull_quote_block',
		)
	);
}
add_action( 'init', 'pqbl_register_pull_quote_block' );

/**
 * Render the Pull Quote Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function pqbl_render_pull_quote_block( $attributes ) {
	// Sanitize attributes.
	$quote    = isset( $attributes['quote'] ) ? sanitize_text_field( $attributes['quote'] ) : '';
	$citation = isset( $attributes['citation'] ) ? sanitize_text_field( $attributes['citation'] ) : '';

	// Escape output.
	$output  = '<div class="wp-block-pull-quote-block-pull-quote">';
	$output .= '<blockquote>' . esc_html( $quote ) . '</blockquote>';
	if ( ! empty( $citation ) ) {
		$output .= '<div class="citation">' . esc_html( $citation ) . '</div>';
	}
	$output .= '</div>';

	return $output;
}

/**
 * Enqueue frontend assets.
 *
 * @return void
 */
function pqbl_enqueue_frontend_assets() {
	wp_enqueue_style( 'pqbl-frontend', plugins_url( 'assets/frontend.css', __FILE__ ), array(), PQBL_VERSION );
}
add_action( 'wp_enqueue_scripts', 'pqbl_enqueue_frontend_assets' );

/**
 * Enqueue editor assets.
 *
 * @return void
 */
function pqbl_enqueue_editor_assets() {
	wp_enqueue_style( 'pqbl-editor', plugins_url( 'assets/editor.css', __FILE__ ), array(), PQBL_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'pqbl_enqueue_editor_assets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function pqbl_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pqbl_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function pqbl_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pqbl_deactivate' );
