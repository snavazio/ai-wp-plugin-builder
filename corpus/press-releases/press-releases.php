<?php
/**
 * Plugin Name:       Press Releases
 * Description:       Custom Post Type for press releases with date meta and shortcode listing.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       press-releases
 *
 * @package Prcs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PRCS_VERSION', '1.0.0' );
define( 'PRCS_FILE', __FILE__ );
define( 'PRCS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function prcs_load_textdomain() {
	load_plugin_textdomain( 'press-releases', false, dirname( plugin_basename( PRCS_FILE ) ) . '/languages' );
}
add_action( 'init', 'prcs_load_textdomain' );

require_once PRCS_PATH . 'includes/post-types.php';
require_once PRCS_PATH . 'includes/meta-boxes.php';
require_once PRCS_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function prcs_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'prcs_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function prcs_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'prcs_deactivate' );
