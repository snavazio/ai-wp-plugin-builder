<?php
/**
 * Plugin Name:       Workshops API
 * Description:       Manages workshops as a custom post type with secure date meta and public REST API access.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       workshops-api
 *
 * @package Wapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WAPI_VERSION', '1.0.0' );
define( 'WAPI_FILE', __FILE__ );
define( 'WAPI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function wapi_load_textdomain() {
	load_plugin_textdomain( 'workshops-api', false, dirname( plugin_basename( WAPI_FILE ) ) . '/languages' );
}
add_action( 'init', 'wapi_load_textdomain' );

// Load class files.
require_once WAPI_PATH . 'includes/class-wapi-post-type.php';

// Initialize features.
Wapi_Post_Type::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function wapi_activate() {
	Wapi_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wapi_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function wapi_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'wapi_deactivate' );
