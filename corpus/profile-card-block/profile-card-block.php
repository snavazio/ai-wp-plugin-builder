<?php
/**
 * Plugin Name:       Profile Card Block
 * Description:       A dynamic server-rendered Gutenberg block for displaying person profile cards with name, title, and bio.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       profile-card-block
 *
 * @package Pccard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PCCARD_VERSION', '1.0.0' );
define( 'PCCARD_FILE', __FILE__ );
define( 'PCCARD_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function pccard_load_textdomain() {
	load_plugin_textdomain( 'profile-card-block', false, dirname( plugin_basename( PCCARD_FILE ) ) . '/languages' );
}
add_action( 'init', 'pccard_load_textdomain' );

/**
 * Register the Profile Card Block.
 *
 * @return void
 */
function pccard_register_profile_card_block() {
	register_block_type(
		'profile-card-block/profile-card',
		array(
			'render_callback' => 'pccard_render_profile_card_block',
		)
	);
}
add_action( 'init', 'pccard_register_profile_card_block' );

/**
 * Render the Profile Card Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function pccard_render_profile_card_block( $attributes ) {
	// Sanitize attributes.
	$name  = isset( $attributes['name'] ) ? sanitize_text_field( $attributes['name'] ) : '';
	$title = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : '';
	$bio   = isset( $attributes['bio'] ) ? sanitize_text_field( $attributes['bio'] ) : '';

	// Escape output.
	$output  = '<div class="pccard-profile-card">';
	$output .= '<h3 class="pccard-name">' . esc_html( $name ) . '</h3>';
	$output .= '<div class="pccard-title">' . esc_html( $title ) . '</div>';
	$output .= '<div class="pccard-bio">' . esc_html( $bio ) . '</div>';
	$output .= '</div>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function pccard_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pccard_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function pccard_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pccard_deactivate' );
