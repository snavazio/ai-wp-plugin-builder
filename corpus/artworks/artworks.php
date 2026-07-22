<?php
/**
 * Plugin Name:       Artworks
 * Description:       A plugin for managing artworks with artist and medium meta.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       artworks
 *
 * @package Artw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARTW_VERSION', '1.0.0' );
define( 'ARTW_FILE', __FILE__ );
define( 'ARTW_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function artw_load_textdomain() {
	load_plugin_textdomain( 'artworks', false, dirname( plugin_basename( ARTW_FILE ) ) . '/languages' );
}
add_action( 'init', 'artw_load_textdomain' );

require_once ARTW_PATH . 'includes/post-types.php';
require_once ARTW_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function artw_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'artw_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function artw_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'artw_deactivate' );
