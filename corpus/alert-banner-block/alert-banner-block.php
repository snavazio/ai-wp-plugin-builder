<?php
/**
 * Plugin Name:       Alert Banner Block
 * Description:       A dynamic server-rendered Gutenberg block that renders a dismissible alert banner with a 'type' attribute (info/success/warning/error) and a message attribute, all escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       alert-banner-block
 *
 * @package Ablk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ABLK_VERSION', '1.0.0' );
define( 'ABLK_FILE', __FILE__ );
define( 'ABLK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ablk_load_textdomain() {
	load_plugin_textdomain( 'alert-banner-block', false, dirname( plugin_basename( ABLK_FILE ) ) . '/languages' );
}
add_action( 'init', 'ablk_load_textdomain' );

/**
 * Register the Alert Banner Block.
 *
 * @return void
 */
function ablk_register_alert_banner_block() {
	register_block_type(
		'alert-banner-block/alert-banner',
		array(
			'render_callback' => 'ablk_render_alert_banner_block',
		)
	);
}
add_action( 'init', 'ablk_register_alert_banner_block' );

/**
 * Render the Alert Banner Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function ablk_render_alert_banner_block( $attributes ) {
	// Sanitize attributes.
	$type    = isset( $attributes['type'] ) ? sanitize_text_field( $attributes['type'] ) : 'info';
	$message = isset( $attributes['message'] ) ? sanitize_text_field( $attributes['message'] ) : '';

	// Validate type against allowed values.
	$allowed_types = array( 'info', 'success', 'warning', 'error' );
	if ( ! in_array( $type, $allowed_types, true ) ) {
		$type = 'info';
	}

	// Escape output.
	$output  = '<div class="ablk-alert ablk-alert-' . esc_attr( $type ) . '">';
	$output .= '<span class="ablk-message">' . esc_html( $message ) . '</span>';
	$output .= '<button class="ablk-dismiss" aria-label="' . esc_attr__( 'Dismiss', 'alert-banner-block' ) . '">×</button>';
	$output .= '</div>';
	return $output;
}

/**
 * Enqueue frontend scripts.
 *
 * @return void
 */
function ablk_enqueue_frontend_scripts() {
	wp_enqueue_script(
		'ablk-frontend',
		ABLK_PATH . 'assets/ablk-frontend.js',
		array( 'jquery' ),
		ABLK_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'ablk_enqueue_frontend_scripts' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function ablk_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ablk_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function ablk_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ablk_deactivate' );
