<?php
/**
 * Plugin Name:       AJAX Quick View
 * Description:       Loads published post content into a modal via AJAX using a shortcode, supporting logged-out users with proper security.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-quick-view
 *
 * @package Aqv1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AQV1_VERSION', '1.0.0' );
define( 'AQV1_FILE', __FILE__ );
define( 'AQV1_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function aqv1_load_textdomain() {
	load_plugin_textdomain( 'ajax-quick-view', false, dirname( plugin_basename( AQV1_FILE ) ) . '/languages' );
}
add_action( 'init', 'aqv1_load_textdomain' );

// Include necessary files.
require_once AQV1_PATH . 'includes/ajax.php';
require_once AQV1_PATH . 'includes/assets.php';
require_once AQV1_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function aqv1_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'aqv1_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function aqv1_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'aqv1_deactivate' );
