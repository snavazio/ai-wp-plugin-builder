<?php
/**
 * Plugin Name:       FAQ API
 * Description:       Provides a custom post type for FAQs with a public REST API endpoint.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       faq-api
 *
 * @package Faqapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FAQAPI_VERSION', '1.0.0' );
define( 'FAQAPI_FILE', __FILE__ );
define( 'FAQAPI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function faqapi_load_textdomain() {
	load_plugin_textdomain( 'faq-api', false, dirname( plugin_basename( FAQAPI_FILE ) ) . '/languages' );
}
add_action( 'init', 'faqapi_load_textdomain' );

// Load class files.
require_once FAQAPI_PATH . 'includes/class-faqapi-post-type.php';

// Initialize features.
Faqapi_Post_Type::init();

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function faqapi_activate() {
	Faqapi_Post_Type::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'faqapi_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function faqapi_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'faqapi_deactivate' );
