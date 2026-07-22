<?php
/**
 * Plugin Name:       Days Online
 * Description:       Outputs the number of whole days since a specified start date via a shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       days-online
 *
 * @package Dolx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DOLX_VERSION', '1.0.0' );
define( 'DOLX_FILE', __FILE__ );
define( 'DOLX_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function dolx_load_textdomain() {
	load_plugin_textdomain( 'days-online', false, dirname( plugin_basename( DOLX_FILE ) ) . '/languages' );
}
add_action( 'init', 'dolx_load_textdomain' );

/**
 * Shortcode handler for [days_online].
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function dolx_shortcode_days_online( $atts ) {
	$atts = shortcode_atts(
		array(
			'start' => '',
		),
		$atts,
		'days_online'
	);

	$start = sanitize_text_field( $atts['start'] );

	// Validate date format (YYYY-MM-DD).
	$date = DateTime::createFromFormat( 'Y-m-d', $start );
	if ( ! $date || $date->format( 'Y-m-d' ) !== $start ) {
		return esc_html( 'Error: Invalid date format. Use YYYY-MM-DD.' );
	}

	$today      = new DateTime();
	$start_date = new DateTime( $start );
	$interval   = $today->diff( $start_date );
	$days       = $interval->days;

	return esc_html( (string) $days );
}

/**
 * Register the days_online shortcode.
 *
 * @return void
 */
function dolx_register_shortcodes() {
	add_shortcode( 'days_online', 'dolx_shortcode_days_online' );
}
add_action( 'init', 'dolx_register_shortcodes' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function dolx_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dolx_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function dolx_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dolx_deactivate' );
