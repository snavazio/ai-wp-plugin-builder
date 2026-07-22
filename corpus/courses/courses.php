<?php
/**
 * Plugin Name:       Courses
 * Description:       A plugin for managing courses with hierarchical subject and level taxonomies.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       courses
 *
 * @package Crs1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CRS1_VERSION', '1.0.0' );
define( 'CRS1_FILE', __FILE__ );
define( 'CRS1_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function crs1_load_textdomain() {
	load_plugin_textdomain( 'courses', false, dirname( plugin_basename( CRS1_FILE ) ) . '/languages' );
}
add_action( 'init', 'crs1_load_textdomain' );

require_once CRS1_PATH . 'includes/post-types.php';
require_once CRS1_PATH . 'includes/taxonomies.php';
require_once CRS1_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function crs1_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'crs1_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function crs1_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'crs1_deactivate' );
