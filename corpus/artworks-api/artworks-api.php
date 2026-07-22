<?php
/**
 * Plugin Name:       Artworks API
 * Description:       Custom post type for artworks with artist metadata and public REST API endpoint
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       artworks-api
 *
 * @package Artw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARTW_VERSION', '1.0.0' );
define( 'ARTW_FILE', __FILE__ );
define( 'ARTW_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function artw_load_textdomain() {
	load_plugin_textdomain( 'artworks-api', false, dirname( plugin_basename( ARTW_FILE ) ) . '/languages' );
}
add_action( 'init', 'artw_load_textdomain' );

// Load class files.
require_once ARTW_PATH . 'includes/class-artw-post-type.php';
require_once ARTW_PATH . 'includes/class-artw-rest-api.php';

// Initialize features.
Artw_Post_Type::init();
Artw_REST_API::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function artw_activate() {
	Artw_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'artw_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function artw_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'artw_deactivate' );
