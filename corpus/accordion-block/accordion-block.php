<?php
/**
 * Plugin Name:       Accordion Block
 * Description:       A dynamic server-rendered Gutenberg block rendering a single accordion item with a title attribute and content attribute, escaped, that expands/collapses.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       accordion-block
 *
 * @package Accb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ACCB_VERSION', '1.0.0' );
define( 'ACCB_FILE', __FILE__ );
define( 'ACCB_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function accb_load_textdomain() {
	load_plugin_textdomain( 'accordion-block', false, dirname( plugin_basename( ACCB_FILE ) ) . '/languages' );
}
add_action( 'init', 'accb_load_textdomain' );

/**
 * Register the Accordion Block.
 *
 * @return void
 */
function accb_register_accordion_block() {
	register_block_type(
		'accordion-block/accordion-item',
		array(
			'render_callback' => 'accb_render_accordion_block',
		)
	);
}
add_action( 'init', 'accb_register_accordion_block' );

/**
 * Render the Accordion Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function accb_render_accordion_block( $attributes ) {
	// Sanitize attributes.
	$title   = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : '';
	$content = isset( $attributes['content'] ) ? sanitize_text_field( $attributes['content'] ) : '';

	// Escape output.
	$output  = '<div class="accordion-item">';
	$output .= '<h3 class="accordion-title">' . esc_html( $title ) . '</h3>';
	$output .= '<div class="accordion-content">' . esc_html( $content ) . '</div>';
	$output .= '</div>';

	return $output;
}

/**
 * Enqueue frontend assets.
 *
 * @return void
 */
function accb_enqueue_frontend_assets() {
	wp_enqueue_style( 'accb-frontend', plugins_url( 'assets/frontend.css', __FILE__ ), array(), ACCB_VERSION );
	wp_enqueue_script( 'accb-frontend', plugins_url( 'assets/frontend.js', __FILE__ ), array(), ACCB_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'accb_enqueue_frontend_assets' );

/**
 * Enqueue editor assets.
 *
 * @return void
 */
function accb_enqueue_editor_assets() {
	wp_enqueue_style( 'accb-editor', plugins_url( 'assets/editor.css', __FILE__ ), array(), ACCB_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'accb_enqueue_editor_assets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function accb_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'accb_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function accb_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'accb_deactivate' );
