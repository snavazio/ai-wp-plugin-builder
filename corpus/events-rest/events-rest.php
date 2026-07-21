<?php
/**
 * Plugin Name:       Events REST
 * Description:       Registers an Events custom post type with a read-only REST API endpoint for published events.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       events-rest
 *
 * @package Evtr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EVTR_VERSION', '1.0.0' );
define( 'EVTR_FILE', __FILE__ );
define( 'EVTR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function evtr_load_textdomain() {
	load_plugin_textdomain( 'events-rest', false, dirname( plugin_basename( EVTR_FILE ) ) . '/languages' );
}
add_action( 'init', 'evtr_load_textdomain' );

// Load class files.
require_once EVTR_PATH . 'includes/class-evtr-post-type.php';
require_once EVTR_PATH . 'includes/class-evtr-rest-api.php';

// Initialize features.
Evtr_Post_Type::init();
Evtr_REST_API::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function evtr_activate() {
	Evtr_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'evtr_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function evtr_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'evtr_deactivate' );
