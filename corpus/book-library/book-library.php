<?php
/**
 * Plugin Name:       Book Library
 * Description:       A plugin for managing books with custom post types, taxonomies, and a shortcode for displaying library content.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       book-library
 *
 * @package Blib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BLIB_VERSION', '1.0.0' );
define( 'BLIB_FILE', __FILE__ );
define( 'BLIB_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function blib_load_textdomain() {
	load_plugin_textdomain( 'book-library', false, dirname( plugin_basename( BLIB_FILE ) ) . '/languages' );
}
add_action( 'init', 'blib_load_textdomain' );

require_once BLIB_PATH . 'includes/post-types.php';
require_once BLIB_PATH . 'includes/taxonomies.php';
require_once BLIB_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function blib_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'blib_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function blib_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'blib_deactivate' );
