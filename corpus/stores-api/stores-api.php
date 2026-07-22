<?php
/**
 * Plugin Name:       Stores API
 * Description:       A Store CPT with address and hours meta (secure meta box) and a public read-only REST endpoint (GET) returning id, title, address, and hours for published stores.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       stores-api
 *
 * @package Strs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STRS_VERSION', '1.0.0' );
define( 'STRS_FILE', __FILE__ );
define( 'STRS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function strs_load_textdomain() {
	load_plugin_textdomain( 'stores-api', false, dirname( plugin_basename( STRS_FILE ) ) . '/languages' );
}
add_action( 'init', 'strs_load_textdomain' );

// Load class files.
require_once STRS_PATH . 'includes/class-strs-post-type.php';
require_once STRS_PATH . 'includes/class-strs-rest-api.php';

// Initialize features.
Strs_Post_Type::init();
Strs_REST_API::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function strs_activate() {
	Strs_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'strs_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function strs_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'strs_deactivate' );
