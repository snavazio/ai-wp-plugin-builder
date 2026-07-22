<?php
/**
 * Plugin Name:       Real Estate Listings
 * Description:       A plugin for managing real estate listings with properties as a custom post type.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       real-estate-listings
 *
 * @package Reli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RELI_VERSION', '1.0.0' );
define( 'RELI_FILE', __FILE__ );
define( 'RELI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function reli_load_textdomain() {
	load_plugin_textdomain( 'real-estate-listings', false, dirname( plugin_basename( RELI_FILE ) ) . '/languages' );
}
add_action( 'init', 'reli_load_textdomain' );

require_once RELI_PATH . 'includes/post-types.php';
require_once RELI_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function reli_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'reli_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function reli_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'reli_deactivate' );
