<?php
/**
 * Plugin Name:       Team API
 * Description:       Member Custom Post Type with role meta and public REST API endpoint.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       team-api
 *
 * @package Tapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TAPI_VERSION', '1.0.0' );
define( 'TAPI_FILE', __FILE__ );
define( 'TAPI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function tapi_load_textdomain() {
	load_plugin_textdomain( 'team-api', false, dirname( plugin_basename( TAPI_FILE ) ) . '/languages' );
}
add_action( 'init', 'tapi_load_textdomain' );

// Load class files.
require_once TAPI_PATH . 'includes/class-tapi-post-type.php';
require_once TAPI_PATH . 'includes/class-tapi-rest-api.php';

// Initialize features.
Tapi_Post_Type::init();
Tapi_REST_API::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function tapi_activate() {
	Tapi_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tapi_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function tapi_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'tapi_deactivate' );
