<?php
/**
 * Plugin Name:       Custom Admin Footer
 * Description:       A plugin to replace the WordPress admin footer text with a customizable value.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-admin-footer
 * Domain Path:       /languages
 *
 * @package Caf1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CAF1_VERSION', '1.0.0' );
define( 'CAF1_FILE', __FILE__ );
define( 'CAF1_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function caf1_load_textdomain() {
	load_plugin_textdomain( 'custom-admin-footer', false, dirname( plugin_basename( CAF1_FILE ) ) . '/languages' );
}
add_action( 'init', 'caf1_load_textdomain' );

// Include feature files.
require_once CAF1_PATH . 'includes/admin-page.php';

/**
 * Replace the admin footer text.
 *
 * @param string $text The current footer text.
 * @return string The modified footer text.
 */
function caf1_footer_text( $text ) {
	$footer_text = get_option( 'caf1_footer_text', '' );
	return $footer_text;
}
add_filter( 'admin_footer_text', 'caf1_footer_text' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function caf1_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'caf1_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function caf1_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'caf1_deactivate' );
