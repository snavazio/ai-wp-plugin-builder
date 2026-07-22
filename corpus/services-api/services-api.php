<?php
/**
 * Plugin Name:       Services API
 * Description:       Custom post type for services with price meta and public REST API endpoint.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       services-api
 *
 * @package Srvapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SRVAPI_VERSION', '1.0.0' );
define( 'SRVAPI_FILE', __FILE__ );
define( 'SRVAPI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function srvapi_load_textdomain() {
	load_plugin_textdomain( 'services-api', false, dirname( plugin_basename( SRVAPI_FILE ) ) . '/languages' );
}
add_action( 'init', 'srvapi_load_textdomain' );

// Load class files.
require_once SRVAPI_PATH . 'includes/class-srvapi-post-type.php';
require_once SRVAPI_PATH . 'includes/class-srvapi-rest-api.php';

// Initialize features.
Srvapi_Post_Type::init();
Srvapi_REST_API::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function srvapi_activate() {
	Srvapi_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'srvapi_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function srvapi_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'srvapi_deactivate' );
