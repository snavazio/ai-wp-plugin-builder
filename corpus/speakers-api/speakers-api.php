<?php
/**
 * Plugin Name:       Speakers API
 * Description:       Custom post type for speakers with REST API endpoint.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       speakers-api
 *
 * @package Spkr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPKR_VERSION', '1.0.0' );
define( 'SPKR_FILE', __FILE__ );
define( 'SPKR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function spkr_load_textdomain() {
	load_plugin_textdomain( 'speakers-api', false, dirname( plugin_basename( SPKR_FILE ) ) . '/languages' );
}
add_action( 'init', 'spkr_load_textdomain' );

// Load class files.
require_once SPKR_PATH . 'includes/class-spkr-post-type.php';

// Initialize features.
Spkr_Post_Type::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function spkr_activate() {
	Spkr_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'spkr_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function spkr_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'spkr_deactivate' );
