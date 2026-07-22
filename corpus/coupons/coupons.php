<?php
/**
 * Plugin Name:       Coupons
 * Description:       A custom post type for managing coupons with code and expiry date, featuring an admin column and shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       coupons
 *
 * @package Cpns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CPNS_VERSION', '1.0.0' );
define( 'CPNS_FILE', __FILE__ );
define( 'CPNS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cpns_load_textdomain() {
	load_plugin_textdomain( 'coupons', false, dirname( plugin_basename( CPNS_FILE ) ) . '/languages' );
}
add_action( 'init', 'cpns_load_textdomain' );

require_once CPNS_PATH . 'includes/post-types.php';
require_once CPNS_PATH . 'includes/meta-boxes.php';
require_once CPNS_PATH . 'includes/admin-columns.php';
require_once CPNS_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cpns_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cpns_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cpns_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cpns_deactivate' );
