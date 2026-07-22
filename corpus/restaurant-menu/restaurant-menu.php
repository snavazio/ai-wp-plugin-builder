<?php
/**
 * Plugin Name:       Restaurant Menu
 * Description:       Manages restaurant menu items with price and dietary notes, featuring a shortcode for displaying published items.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       restaurant-menu
 *
 * @package Rmenu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RMENU_VERSION', '1.0.0' );
define( 'RMENU_FILE', __FILE__ );
define( 'RMENU_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rmenu_load_textdomain() {
	load_plugin_textdomain( 'restaurant-menu', false, dirname( plugin_basename( RMENU_FILE ) ) . '/languages' );
}
add_action( 'init', 'rmenu_load_textdomain' );

require_once RMENU_PATH . 'includes/post-types.php';
require_once RMENU_PATH . 'includes/meta-boxes.php';
require_once RMENU_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function rmenu_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rmenu_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rmenu_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rmenu_deactivate' );
