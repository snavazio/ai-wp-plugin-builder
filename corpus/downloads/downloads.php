<?php
/**
 * Plugin Name:       Downloads
 * Description:       A Download CPT with a file-URL meta and a download-count meta (secure meta box) and a [downloads] shortcode listing published downloads as escaped links.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       downloads
 *
 * @package Dlm1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DLM1_VERSION', '1.0.0' );
define( 'DLM1_FILE', __FILE__ );
define( 'DLM1_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function dlm1_load_textdomain() {
	load_plugin_textdomain( 'downloads', false, dirname( plugin_basename( DLM1_FILE ) ) . '/languages' );
}
add_action( 'init', 'dlm1_load_textdomain' );

require_once DLM1_PATH . 'includes/post-types.php';
require_once DLM1_PATH . 'includes/meta-boxes.php';
require_once DLM1_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function dlm1_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dlm1_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function dlm1_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dlm1_deactivate' );
