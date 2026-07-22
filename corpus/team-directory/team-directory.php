<?php
/**
 * Plugin Name:       Team Directory
 * Description:       A plugin for managing team members with a custom post type and department taxonomy.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       team-directory
 *
 * @package Tdir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TDIR_VERSION', '1.0.0' );
define( 'TDIR_FILE', __FILE__ );
define( 'TDIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function tdir_load_textdomain() {
	load_plugin_textdomain( 'team-directory', false, dirname( plugin_basename( TDIR_FILE ) ) . '/languages' );
}
add_action( 'init', 'tdir_load_textdomain' );

require_once TDIR_PATH . 'includes/post-types.php';
require_once TDIR_PATH . 'includes/taxonomies.php';
require_once TDIR_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function tdir_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tdir_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function tdir_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'tdir_deactivate' );
