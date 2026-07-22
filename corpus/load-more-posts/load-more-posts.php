<?php
/**
 * Plugin Name:       Load More Posts
 * Description:       A [load_more] shortcode listing recent published posts with a button that loads the next page via AJAX (available logged-out).
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       load-more-posts
 *
 * @package Lmpa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LMPA_VERSION', '1.0.0' );
define( 'LMPA_FILE', __FILE__ );
define( 'LMPA_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function lmpa_load_textdomain() {
	load_plugin_textdomain( 'load-more-posts', false, dirname( plugin_basename( LMPA_FILE ) ) . '/languages' );
}
add_action( 'init', 'lmpa_load_textdomain' );

// Include necessary files.
require_once LMPA_PATH . 'includes/ajax.php';
require_once LMPA_PATH . 'includes/assets.php';
require_once LMPA_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function lmpa_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'lmpa_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function lmpa_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'lmpa_deactivate' );
