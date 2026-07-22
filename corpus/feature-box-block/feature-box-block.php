<?php
/**
 * Plugin Name:       Feature Box Block
 * Description:       A dynamic server-rendered Gutenberg block rendering a feature box with an icon name, heading, and description attribute, all escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       feature-box-block
 *
 * @package Fbox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FBOX_VERSION', '1.0.0' );
define( 'FBOX_FILE', __FILE__ );
define( 'FBOX_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function fbox_load_textdomain() {
	load_plugin_textdomain( 'feature-box-block', false, dirname( plugin_basename( FBOX_FILE ) ) . '/languages' );
}
add_action( 'init', 'fbox_load_textdomain' );

/**
 * Register the Feature Box Block.
 *
 * @return void
 */
function fbox_register_feature_box_block() {
	register_block_type(
		'fbox/feature-box',
		array(
			'render_callback' => 'fbox_render_feature_box_block',
		)
	);
}
add_action( 'init', 'fbox_register_feature_box_block' );

/**
 * Render the Feature Box Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function fbox_render_feature_box_block( $attributes ) {
	// Sanitize attributes.
	$icon        = isset( $attributes['icon'] ) ? sanitize_text_field( $attributes['icon'] ) : '';
	$heading     = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
	$description = isset( $attributes['description'] ) ? sanitize_text_field( $attributes['description'] ) : '';

	// Escape output.
	$output  = '<div class="fbox-feature-box">';
	$output .= '<div class="fbox-icon">' . esc_html( $icon ) . '</div>';
	$output .= '<h3 class="fbox-heading">' . esc_html( $heading ) . '</h3>';
	$output .= '<p class="fbox-description">' . esc_html( $description ) . '</p>';
	$output .= '</div>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function fbox_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'fbox_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function fbox_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'fbox_deactivate' );
