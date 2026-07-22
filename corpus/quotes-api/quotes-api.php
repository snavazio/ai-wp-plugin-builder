<?php
/**
 * Plugin Name:       Quotes API
 * Description:       Custom Post Type for quotes with author meta and public REST API endpoint
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       quotes-api
 *
 * @package Qapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'QAPI_VERSION', '1.0.0' );
define( 'QAPI_FILE', __FILE__ );
define( 'QAPI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function qapi_load_textdomain() {
	load_plugin_textdomain( 'quotes-api', false, dirname( plugin_basename( QAPI_FILE ) ) . '/languages' );
}
add_action( 'init', 'qapi_load_textdomain' );

// Load class files.
require_once QAPI_PATH . 'includes/class-qapi-post-type.php';
require_once QAPI_PATH . 'includes/class-qapi-rest-api.php';

// Initialize features.
Qapi_Post_Type::init();
Qapi_REST_API::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function qapi_activate() {
	Qapi_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'qapi_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function qapi_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'qapi_deactivate' );
