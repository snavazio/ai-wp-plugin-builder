<?php
/**
 * Plugin Name:       Job Board
 * Description:       A job board plugin featuring a custom post type with location and salary meta, hierarchical categories, admin columns, shortcodes, and a public REST API.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       job-board
 * Domain Path:       /languages
 *
 * @package Jbrd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JBRD_VERSION', '1.0.0' );
define( 'JBRD_FILE', __FILE__ );
define( 'JBRD_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function jbrd_load_textdomain() {
	load_plugin_textdomain( 'job-board', false, dirname( plugin_basename( JBRD_FILE ) ) . '/languages' );
}
add_action( 'init', 'jbrd_load_textdomain' );

require_once JBRD_PATH . 'includes/post-types.php';
require_once JBRD_PATH . 'includes/taxonomies.php';
require_once JBRD_PATH . 'includes/shortcodes.php';
require_once JBRD_PATH . 'includes/rest.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function jbrd_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'jbrd_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function jbrd_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'jbrd_deactivate' );
