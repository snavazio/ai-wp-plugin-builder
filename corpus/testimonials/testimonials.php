<?php
/**
 * Plugin Name:       Testimonials
 * Description:       Manage client testimonials with ratings, customer details, and admin display.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       testimonials
 *
 * @package Tmnl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TMNL_VERSION', '1.0.0' );
define( 'TMNL_FILE', __FILE__ );
define( 'TMNL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function tmnl_load_textdomain() {
	load_plugin_textdomain( 'testimonials', false, dirname( plugin_basename( TMNL_FILE ) ) . '/languages' );
}
add_action( 'init', 'tmnl_load_textdomain' );

// Include feature files.
require_once TMNL_PATH . 'includes/post-type.php';
require_once TMNL_PATH . 'includes/meta-box.php';
require_once TMNL_PATH . 'includes/admin-columns.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function tmnl_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tmnl_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function tmnl_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'tmnl_deactivate' );
