<?php
/**
 * Plugin Name:       Event RSVP
 * Description:       Event Custom Post Type with RSVP functionality including date meta, shortcode, and AJAX handler.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       event-rsvp
 *
 * @package Ersv
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ERSV_VERSION', '1.0.0' );
define( 'ERSV_FILE', __FILE__ );
define( 'ERSV_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ersv_load_textdomain() {
	load_plugin_textdomain( 'event-rsvp', false, dirname( plugin_basename( ERSV_FILE ) ) . '/languages' );
}
add_action( 'init', 'ersv_load_textdomain' );

// Include necessary files.
require_once ERSV_PATH . 'includes/post-types.php';
require_once ERSV_PATH . 'includes/shortcode.php';
require_once ERSV_PATH . 'includes/ajax.php';
require_once ERSV_PATH . 'includes/assets.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function ersv_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ersv_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function ersv_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ersv_deactivate' );
