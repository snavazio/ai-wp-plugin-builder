<?php
/**
 * Plugin Name:       Contact Info
 * Description:       Stores and displays contact information (phone, email, address) via a settings page and shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       contact-info
 *
 * @package Cinfo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CINFO_VERSION', '1.0.0' );
define( 'CINFO_FILE', __FILE__ );
define( 'CINFO_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cinfo_load_textdomain() {
	load_plugin_textdomain( 'contact-info', false, dirname( plugin_basename( CINFO_FILE ) ) . '/languages' );
}
add_action( 'init', 'cinfo_load_textdomain' );

// Include feature files.
require_once CINFO_PATH . 'includes/admin-page.php';

/**
 * Shortcode for displaying contact info.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function cinfo_shortcode( $atts ) {
	$contact_info = get_option(
		'cinfo_contact_info',
		array(
			'phone'   => '',
			'email'   => '',
			'address' => '',
		)
	);
	$output       = '';

	if ( ! empty( $contact_info['phone'] ) ) {
		$output .= '<p>' . esc_html__( 'Phone: ', 'contact-info' ) . esc_html( $contact_info['phone'] ) . '</p>';
	}
	if ( ! empty( $contact_info['email'] ) ) {
		$output .= '<p>' . esc_html__( 'Email: ', 'contact-info' ) . esc_html( $contact_info['email'] ) . '</p>';
	}
	if ( ! empty( $contact_info['address'] ) ) {
		$output .= '<p>' . esc_html__( 'Address: ', 'contact-info' ) . esc_html( $contact_info['address'] ) . '</p>';
	}

	return $output;
}
add_shortcode( 'contact_info', 'cinfo_shortcode' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cinfo_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cinfo_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cinfo_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cinfo_deactivate' );
