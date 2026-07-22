<?php
/**
 * Plugin Name:       Announcements API
 * Description:       Provides a public REST endpoint for published announcements with proper security and sanitization.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       announcements-api
 *
 * @package Annapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANNAPI_VERSION', '1.0.0' );
define( 'ANNAPI_FILE', __FILE__ );
define( 'ANNAPI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function annapi_load_textdomain() {
	load_plugin_textdomain( 'announcements-api', false, dirname( plugin_basename( ANNAPI_FILE ) ) . '/languages' );
}
add_action( 'init', 'annapi_load_textdomain' );

// Load class files.
require_once ANNAPI_PATH . 'includes/class-annapi-post-type.php';
require_once ANNAPI_PATH . 'includes/class-annapi-rest-api.php';

// Initialize features.
Annapi_Post_Type::init();
Annapi_REST_API::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function annapi_activate() {
	Annapi_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'annapi_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function annapi_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'annapi_deactivate' );
