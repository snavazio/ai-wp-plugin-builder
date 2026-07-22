<?php
/**
 * Plugin Name:       Sponsors API
 * Description:       A Sponsor CPT with a secure website meta box and a public REST endpoint for published sponsors.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sponsors-api
 *
 * @package Spns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPNS_VERSION', '1.0.0' );
define( 'SPNS_FILE', __FILE__ );
define( 'SPNS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function spns_load_textdomain() {
	load_plugin_textdomain( 'sponsors-api', false, dirname( plugin_basename( SPNS_FILE ) ) . '/languages' );
}
add_action( 'init', 'spns_load_textdomain' );

// Load class files.
require_once SPNS_PATH . 'includes/class-spns-post-type.php';

// Initialize features.
Spns_Post_Type::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function spns_activate() {
	Spns_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'spns_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function spns_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'spns_deactivate' );
