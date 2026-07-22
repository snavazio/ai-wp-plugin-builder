<?php
/**
 * Plugin Name:       Gallery API
 * Description:       A Photo Custom Post Type with secure caption meta and a public REST API endpoint for published photos.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gallery-api
 *
 * @package Galapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GALAPI_VERSION', '1.0.0' );
define( 'GALAPI_FILE', __FILE__ );
define( 'GALAPI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function galapi_load_textdomain() {
	load_plugin_textdomain( 'gallery-api', false, dirname( plugin_basename( GALAPI_FILE ) ) . '/languages' );
}
add_action( 'init', 'galapi_load_textdomain' );

// Load class files.
require_once GALAPI_PATH . 'includes/class-galapi-post-type.php';
require_once GALAPI_PATH . 'includes/class-galapi-rest-api.php';

// Initialize features.
Galapi_Post_Type::init();
Galapi_REST_API::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function galapi_activate() {
	Galapi_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'galapi_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function galapi_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'galapi_deactivate' );
