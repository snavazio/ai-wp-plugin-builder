<?php
/**
 * Plugin Name:       Business Hours
 * Description:       A plugin to manage and display business hours with a settings page and shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       business-hours
 *
 * @package Bhrs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BHRS_VERSION', '1.0.0' );
define( 'BHRS_FILE', __FILE__ );
define( 'BHRS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function bhrs_load_textdomain() {
	load_plugin_textdomain( 'business-hours', false, dirname( plugin_basename( BHRS_FILE ) ) . '/languages' );
}
add_action( 'init', 'bhrs_load_textdomain' );

// Include feature files.
require_once BHRS_PATH . 'includes/admin-page.php';

/**
 * Shortcode for displaying business hours.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bhrs_shortcode( $atts ) {
	$business_hours = get_option( 'bhrs_business_hours', array() );
	$output         = '<div class="bhrs-business-hours">';

	if ( ! empty( $business_hours ) ) {
		foreach ( $business_hours as $day => $hours ) {
			if ( ! empty( $hours['open'] ) && ! empty( $hours['close'] ) ) {
				$output .= '<p>' . esc_html( ucfirst( $day ) ) . ': ' . esc_html( $hours['open'] ) . ' - ' . esc_html( $hours['close'] ) . '</p>';
			}
		}
	} else {
		$output .= '<p>' . esc_html__( 'Business hours not set.', 'business-hours' ) . '</p>';
	}

	$output .= '</div>';

	return $output;
}
add_shortcode( 'business_hours', 'bhrs_shortcode' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function bhrs_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bhrs_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function bhrs_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'bhrs_deactivate' );
