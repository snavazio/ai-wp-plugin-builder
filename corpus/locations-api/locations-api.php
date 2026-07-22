<?php
/**
 * Plugin Name:       Locations API
 * Description:       A Location CPT with latitude and longitude meta (secure meta box, sanitized) and a public read-only REST endpoint (GET) returning id, title, lat, and lng for published locations.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       locations-api
 *
 * @package Locapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LOCAPI_VERSION', '1.0.0' );
define( 'LOCAPI_FILE', __FILE__ );
define( 'LOCAPI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function locapi_load_textdomain() {
	load_plugin_textdomain( 'locations-api', false, dirname( plugin_basename( LOCAPI_FILE ) ) . '/languages' );
}
add_action( 'init', 'locapi_load_textdomain' );

// Load class files.
require_once LOCAPI_PATH . 'includes/class-locapi-post-type.php';
require_once LOCAPI_PATH . 'includes/class-locapi-rest-api.php';

// Initialize features.
Locapi_Post_Type::init();
Locapi_REST_API::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function locapi_activate() {
	Locapi_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'locapi_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function locapi_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'locapi_deactivate' );
