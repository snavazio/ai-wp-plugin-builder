<?php
/**
 * Plugin Name:       Notice Block
 * Description:       A dynamic server-rendered Gutenberg block that outputs an info/warning notice with a type attribute and message, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       notice-block
 *
 * @package Nblock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NBLOCK_VERSION', '1.0.0' );
define( 'NBLOCK_FILE', __FILE__ );
define( 'NBLOCK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function nblock_load_textdomain() {
	load_plugin_textdomain( 'notice-block', false, dirname( plugin_basename( NBLOCK_FILE ) ) . '/languages' );
}
add_action( 'init', 'nblock_load_textdomain' );

/**
 * Register the Notice Block.
 *
 * @return void
 */
function nblock_register_notice_block() {
	register_block_type(
		'nblock/notice-block',
		array(
			'render_callback' => 'nblock_render_notice_block',
		)
	);
}
add_action( 'init', 'nblock_register_notice_block' );

/**
 * Render the Notice Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function nblock_render_notice_block( $attributes ) {
	// Sanitize attributes.
	$type    = isset( $attributes['type'] ) ? sanitize_text_field( $attributes['type'] ) : 'info';
	$message = isset( $attributes['message'] ) ? sanitize_text_field( $attributes['message'] ) : '';

	// Escape output.
	$output = '<div class="notice-block notice-block-' . esc_attr( $type ) . '">' . esc_html( $message ) . '</div>';
	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function nblock_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'nblock_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function nblock_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'nblock_deactivate' );
