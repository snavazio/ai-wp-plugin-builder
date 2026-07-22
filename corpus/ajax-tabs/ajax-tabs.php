<?php
/**
 * Plugin Name:       AJAX Tabs
 * Description:       Render tabbed interface with AJAX-loaded post excerpts using [tabs] shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-tabs
 *
 * @package Ajxt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AJXT_VERSION', '1.0.0' );
define( 'AJXT_FILE', __FILE__ );
define( 'AJXT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ajxt_load_textdomain() {
	load_plugin_textdomain( 'ajax-tabs', false, dirname( plugin_basename( AJXT_FILE ) ) . '/languages' );
}
add_action( 'init', 'ajxt_load_textdomain' );

// Include necessary files.
require_once AJXT_PATH . 'includes/ajax.php';
require_once AJXT_PATH . 'includes/assets.php';
require_once AJXT_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function ajxt_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ajxt_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function ajxt_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ajxt_deactivate' );
