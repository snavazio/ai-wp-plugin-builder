<?php
/**
 * Plugin Name:       Icon List Block
 * Description:       A dynamic Gutenberg block rendering a list of items with icon names and text attributes, with full output escaping and input sanitization.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       icon-list-block
 *
 * @package Ilblock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ILBLOCK_VERSION', '1.0.0' );
define( 'ILBLOCK_FILE', __FILE__ );
define( 'ILBLOCK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ilblock_load_textdomain() {
	load_plugin_textdomain( 'icon-list-block', false, dirname( plugin_basename( ILBLOCK_FILE ) ) . '/languages' );
}
add_action( 'init', 'ilblock_load_textdomain' );

/**
 * Register the Icon List Block.
 *
 * @return void
 */
function ilblock_register_icon_list_block() {
	register_block_type(
		'ilblock/icon-list',
		array(
			'render_callback' => 'ilblock_render_icon_list_block',
		)
	);
}
add_action( 'init', 'ilblock_register_icon_list_block' );

/**
 * Render the Icon List Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function ilblock_render_icon_list_block( $attributes ) {
	// Sanitize attributes.
	$items           = isset( $attributes['items'] ) ? $attributes['items'] : array();
	$sanitized_items = array();

	foreach ( $items as $item ) {
		$icon              = isset( $item['icon'] ) ? sanitize_text_field( $item['icon'] ) : '';
		$text              = isset( $item['text'] ) ? sanitize_text_field( $item['text'] ) : '';
		$sanitized_items[] = array(
			'icon' => $icon,
			'text' => $text,
		);
	}

	// Escape output.
	$output = '<ul class="ilblock-icon-list">';
	foreach ( $sanitized_items as $item ) {
		$output .= '<li class="ilblock-icon-list-item">';
		$output .= '<span class="ilblock-icon">' . esc_html( $item['icon'] ) . '</span>';
		$output .= '<span class="ilblock-text">' . esc_html( $item['text'] ) . '</span>';
		$output .= '</li>';
	}
	$output .= '</ul>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function ilblock_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ilblock_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function ilblock_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ilblock_deactivate' );
