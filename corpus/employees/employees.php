<?php
/**
 * Plugin Name:       Employees
 * Description:       Employee Custom Post Type with department and email meta, admin column, and shortcode listing.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       employees
 *
 * @package Empc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EMPC_VERSION', '1.0.0' );
define( 'EMPC_FILE', __FILE__ );
define( 'EMPC_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function empc_load_textdomain() {
	load_plugin_textdomain( 'employees', false, dirname( plugin_basename( EMPC_FILE ) ) . '/languages' );
}
add_action( 'init', 'empc_load_textdomain' );

require_once EMPC_PATH . 'includes/post-types.php';
require_once EMPC_PATH . 'includes/meta-boxes.php';
require_once EMPC_PATH . 'includes/admin-columns.php';
require_once EMPC_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function empc_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'empc_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function empc_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'empc_deactivate' );
