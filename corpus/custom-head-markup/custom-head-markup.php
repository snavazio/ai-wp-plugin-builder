<?php
/**
 * Plugin Name:       Custom Head Markup
 * Description:       Adds a settings page for custom <head> markup (e.g., analytics/meta tags) output via wp_head, with admin-only access and full sanitization.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-head-markup
 *
 * @package Chmp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CHMP_VERSION', '1.0.0' );
define( 'CHMP_FILE', __FILE__ );
define( 'CHMP_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function chmp_load_textdomain() {
	load_plugin_textdomain( 'custom-head-markup', false, dirname( plugin_basename( CHMP_FILE ) ) . '/languages' );
}
add_action( 'init', 'chmp_load_textdomain' );

// Include feature files.
require_once CHMP_PATH . 'includes/admin-page.php';

/**
 * Output custom head markup via wp_head.
 *
 * @return void
 */
function chmp_output_head_markup() {
	$markup = get_option( 'chmp_head_markup', '' );
	if ( ! empty( $markup ) ) {
		echo wp_kses_post( $markup );
	}
}
add_action( 'wp_head', 'chmp_output_head_markup' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function chmp_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'chmp_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function chmp_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'chmp_deactivate' );
