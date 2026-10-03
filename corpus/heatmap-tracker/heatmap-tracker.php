<?php
/**
 * Plugin Name:       Heatmap Tracker
 * Description:       Records visitor click positions and scroll depth on chosen pages and posts, and shows them as a device-split heatmap overlay to administrators on the live page.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       heatmap-tracker
 *
 * @package Hmtrk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HMTRK_VERSION', '1.0.0' );
define( 'HMTRK_FILE', __FILE__ );
define( 'HMTRK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function hmtrk_load_textdomain() {
	load_plugin_textdomain( 'heatmap-tracker', false, dirname( plugin_basename( HMTRK_FILE ) ) . '/languages' );
}
add_action( 'init', 'hmtrk_load_textdomain' );

require_once HMTRK_PATH . 'includes/db.php';
require_once HMTRK_PATH . 'includes/settings.php';
require_once HMTRK_PATH . 'includes/rest.php';
require_once HMTRK_PATH . 'includes/frontend.php';

/**
 * Activation hook. Create the events table.
 *
 * @return void
 */
function hmtrk_activate() {
	hmtrk_install_table();
}
register_activation_hook( __FILE__, 'hmtrk_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function hmtrk_deactivate() {
	// Nothing to clean up on deactivation; data is kept.
}
register_deactivation_hook( __FILE__, 'hmtrk_deactivate' );
