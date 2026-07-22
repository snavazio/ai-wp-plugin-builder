<?php
/**
 * Plugin Name:       Testimonials API
 * Description:       Testimonial CPT with author meta field and public REST endpoint for published testimonials.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       testimonials-api
 *
 * @package Tstm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TSTM_VERSION', '1.0.0' );
define( 'TSTM_FILE', __FILE__ );
define( 'TSTM_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function tstm_load_textdomain() {
	load_plugin_textdomain( 'testimonials-api', false, dirname( plugin_basename( TSTM_FILE ) ) . '/languages' );
}
add_action( 'init', 'tstm_load_textdomain' );

// Load class files.
require_once TSTM_PATH . 'includes/class-tstm-post-type.php';
require_once TSTM_PATH . 'includes/class-tstm-rest-api.php';

// Initialize features.
Tstm_Post_Type::init();
Tstm_REST_API::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function tstm_activate() {
	Tstm_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tstm_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function tstm_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'tstm_deactivate' );
