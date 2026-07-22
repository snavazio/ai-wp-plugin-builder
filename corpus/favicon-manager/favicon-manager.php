<?php
/**
 * Plugin Name:       Favicon Manager
 * Description:       Manages site favicon via settings page with URL field and wp_head output.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       favicon-manager
 *
 * @package Fvman
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FVMAN_VERSION', '1.0.0' );
define( 'FVMAN_FILE', __FILE__ );
define( 'FVMAN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function fvman_load_textdomain() {
	load_plugin_textdomain( 'favicon-manager', false, dirname( plugin_basename( FVMAN_FILE ) ) . '/languages' );
}
add_action( 'init', 'fvman_load_textdomain' );

// Include feature files.
require_once FVMAN_PATH . 'includes/admin-page.php';

/**
 * Output favicon link tag via wp_head.
 *
 * @return void
 */
function fvman_output_favicon() {
	$url = get_option( 'fvman_favicon_url', '' );
	if ( ! empty( $url ) ) {
		echo '<link rel="icon" href="' . esc_url( $url ) . '" />' . "\n";
	}
}
add_action( 'wp_head', 'fvman_output_favicon' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function fvman_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'fvman_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function fvman_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'fvman_deactivate' );
