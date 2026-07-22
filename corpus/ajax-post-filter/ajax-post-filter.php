<?php
/**
 * Plugin Name:       AJAX Post Filter
 * Description:       A shortcode with category buttons that fetches published posts via AJAX, allowing logged-out users with proper nonce and capability checks.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-post-filter
 *
 * @package Apf1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'APF1_VERSION', '1.0.0' );
define( 'APF1_FILE', __FILE__ );
define( 'APF1_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function apf1_load_textdomain() {
	load_plugin_textdomain( 'ajax-post-filter', false, dirname( plugin_basename( APF1_FILE ) ) . '/languages' );
}
add_action( 'init', 'apf1_load_textdomain' );

// Include necessary files.
require_once APF1_PATH . 'includes/ajax.php';
require_once APF1_PATH . 'includes/assets.php';
require_once APF1_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function apf1_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'apf1_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function apf1_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'apf1_deactivate' );
