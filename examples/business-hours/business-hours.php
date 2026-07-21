<?php
/**
 * Plugin Name:       Business Hours
 * Description:       Display business opening hours via settings page and shortcode.
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

// Load admin settings.
require_once BHRS_PATH . 'includes/admin-settings.php';

// Load shortcode.
require_once BHRS_PATH . 'includes/shortcode.php';

/**
 * Activation hook.
 *
 * @return void
 */
function bhrs_activate() {
	// No activation tasks needed.
}
register_activation_hook( __FILE__, 'bhrs_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function bhrs_deactivate() {
	// No deactivation tasks needed.
}
register_deactivation_hook( __FILE__, 'bhrs_deactivate' );
