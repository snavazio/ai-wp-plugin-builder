<?php
/**
 * Plugin Name:       Timeline Block
 * Description:       A dynamic server-rendered Gutenberg block rendering a single timeline entry with date, title, and description attributes, escaped.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       timeline-block
 *
 * @package Tlmb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TLMB_VERSION', '1.0.0' );
define( 'TLMB_FILE', __FILE__ );
define( 'TLMB_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function tlmb_load_textdomain() {
	load_plugin_textdomain( 'timeline-block', false, dirname( plugin_basename( TLMB_FILE ) ) . '/languages' );
}
add_action( 'init', 'tlmb_load_textdomain' );

/**
 * Register the Timeline Block.
 *
 * @return void
 */
function tlmb_register_timeline_block() {
	register_block_type(
		'timeline-block/timeline-entry',
		array(
			'render_callback' => 'tlmb_render_timeline_block',
		)
	);
}
add_action( 'init', 'tlmb_register_timeline_block' );

/**
 * Render the Timeline Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function tlmb_render_timeline_block( $attributes ) {
	// Sanitize attributes.
	$date  = isset( $attributes['date'] ) ? sanitize_text_field( $attributes['date'] ) : '';
	$title = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : '';
	$desc  = isset( $attributes['description'] ) ? sanitize_text_field( $attributes['description'] ) : '';

	// Escape output.
	$output  = '<div class="tlmb-timeline-entry">';
	$output .= '<div class="tlmb-date">' . esc_html( $date ) . '</div>';
	$output .= '<h3 class="tlmb-title">' . esc_html( $title ) . '</h3>';
	$output .= '<div class="tlmb-description">' . esc_html( $desc ) . '</div>';
	$output .= '</div>';

	return $output;
}

/**
 * Enqueue frontend assets.
 *
 * @return void
 */
function tlmb_enqueue_frontend_assets() {
	wp_enqueue_style( 'tlmb-frontend', plugins_url( 'assets/frontend.css', __FILE__ ), array(), TLMB_VERSION );
}
add_action( 'wp_enqueue_scripts', 'tlmb_enqueue_frontend_assets' );

/**
 * Enqueue editor assets.
 *
 * @return void
 */
function tlmb_enqueue_editor_assets() {
	wp_enqueue_style( 'tlmb-editor', plugins_url( 'assets/editor.css', __FILE__ ), array(), TLMB_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'tlmb_enqueue_editor_assets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function tlmb_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tlmb_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function tlmb_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'tlmb_deactivate' );
