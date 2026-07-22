<?php
/**
 * Plugin Name:       AJAX Availability Check
 * Description:       Provides an [availability] shortcode that checks date availability via AJAX against stored booked dates, allowing logged-out users.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-availability-check
 *
 * @package Avch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AVCH_VERSION', '1.0.0' );
define( 'AVCH_FILE', __FILE__ );
define( 'AVCH_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function avch_load_textdomain() {
	load_plugin_textdomain( 'ajax-availability-check', false, dirname( plugin_basename( AVCH_FILE ) ) . '/languages' );
}
add_action( 'init', 'avch_load_textdomain' );

// Include required files before registering features.
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/assets.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function avch_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'avch_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function avch_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'avch_deactivate' );
