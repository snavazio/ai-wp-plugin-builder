<?php
/**
 * Plugin Name:       Pricing Table Block
 * Description:       A dynamic server-rendered Gutenberg block rendering a single pricing tier: plan name, price, and a list of features from block attributes, all escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pricing-table-block
 *
 * @package Ptbl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PTBL_VERSION', '1.0.0' );
define( 'PTBL_FILE', __FILE__ );
define( 'PTBL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ptbl_load_textdomain() {
	load_plugin_textdomain( 'pricing-table-block', false, dirname( plugin_basename( PTBL_FILE ) ) . '/languages' );
}
add_action( 'init', 'ptbl_load_textdomain' );

/*
 * TODO(coder): require your include files here and register your features
 * (CPTs, admin pages, shortcodes/blocks, REST routes) following the hard rules in CLAUDE.md.
 * Every executable file needs the ABSPATH guard. Sanitize input, escape output, and pair every
 * state-changing action with BOTH a nonce check AND current_user_can().
 */

/**
 * Register the Pricing Table Block.
 *
 * @return void
 */
function ptbl_register_pricing_table_block() {
	register_block_type(
		'pricing-table-block/pricing-table',
		array(
			'render_callback' => 'ptbl_render_pricing_table_block',
		)
	);
}
add_action( 'init', 'ptbl_register_pricing_table_block' );

/**
 * Render the Pricing Table Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function ptbl_render_pricing_table_block( $attributes ) {
	// Sanitize attributes.
	$plan_name = isset( $attributes['planName'] ) ? sanitize_text_field( $attributes['planName'] ) : '';
	$price     = isset( $attributes['price'] ) ? sanitize_text_field( $attributes['price'] ) : '';
	$features  = isset( $attributes['features'] ) ? array_map( 'sanitize_text_field', (array) $attributes['features'] ) : array();

	// Escape output.
	$output  = '<div class="ptbl-pricing-table">';
	$output .= '<h3 class="ptbl-plan-name">' . esc_html( $plan_name ) . '</h3>';
	$output .= '<div class="ptbl-price">' . esc_html( $price ) . '</div>';
	$output .= '<ul class="ptbl-features">';
	foreach ( $features as $feature ) {
		$output .= '<li class="ptbl-feature">' . esc_html( $feature ) . '</li>';
	}
	$output .= '</ul>';
	$output .= '</div>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function ptbl_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ptbl_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function ptbl_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ptbl_deactivate' );
