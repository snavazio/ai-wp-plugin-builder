<?php
/**
 * Plugin Name:       Game Catalog
 * Description:       A custom post type for games with release year, platform, and genre taxonomies, plus a shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       game-catalog
 *
 * @package Gcat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GCAT_VERSION', '1.0.0' );
define( 'GCAT_FILE', __FILE__ );
define( 'GCAT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function gcat_load_textdomain() {
	load_plugin_textdomain( 'game-catalog', false, dirname( plugin_basename( GCAT_FILE ) ) . '/languages' );
}
add_action( 'init', 'gcat_load_textdomain' );

require_once GCAT_PATH . 'includes/post-types.php';
require_once GCAT_PATH . 'includes/taxonomies.php';
require_once GCAT_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function gcat_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gcat_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function gcat_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'gcat_deactivate' );
