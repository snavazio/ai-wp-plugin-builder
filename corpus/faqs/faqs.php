<?php
/**
 * Plugin Name:       FAQs
 * Description:       Creates an FAQ custom post type with question/answer fields and a shortcode to display FAQs as an accordion.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       faqs
 *
 * @package Faqs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FAQS_VERSION', '1.0.0' );
define( 'FAQS_FILE', __FILE__ );
define( 'FAQS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function faqs_load_textdomain() {
	load_plugin_textdomain( 'faqs', false, dirname( plugin_basename( FAQS_FILE ) ) . '/languages' );
}
add_action( 'init', 'faqs_load_textdomain' );

require_once FAQS_PATH . 'includes/post-types.php';
require_once FAQS_PATH . 'includes/assets.php';
require_once FAQS_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Flush rewrite rules after registering CPT.
 *
 * @return void
 */
function faqs_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'faqs_activate' );

/**
 * Deactivation hook. Flush rewrite rules.
 *
 * @return void
 */
function faqs_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'faqs_deactivate' );
