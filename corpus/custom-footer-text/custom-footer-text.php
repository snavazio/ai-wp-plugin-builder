<?php
/**
 * Plugin Name:       Custom Footer Text
 * Description:       Adds a settings page to customize site footer text with basic HTML support.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-footer-text
 *
 * @package Cftx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CFTX_VERSION', '1.0.0' );
define( 'CFTX_FILE', __FILE__ );
define( 'CFTX_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cftx_load_textdomain() {
	load_plugin_textdomain( 'custom-footer-text', false, dirname( plugin_basename( CFTX_FILE ) ) . '/languages' );
}
add_action( 'init', 'cftx_load_textdomain' );

// Include feature files.
require_once CFTX_PATH . 'includes/admin-page.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cftx_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cftx_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cftx_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cftx_deactivate' );
