<?php
/**
 * Plugin Name:       Tag List Widget
 * Description:       A classic sidebar widget listing the N most-used post tags as escaped links, with a configurable count and title. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tag-list-widget
 *
 * @package Tlw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TLW_VERSION', '1.0.0' );
define( 'TLW_FILE', __FILE__ );
define( 'TLW_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function tlw_load_textdomain() {
	load_plugin_textdomain( 'tag-list-widget', false, dirname( plugin_basename( TLW_FILE ) ) . '/languages' );
}
add_action( 'init', 'tlw_load_textdomain' );

// Include widget class.
require_once TLW_PATH . 'includes/widget.php';

/**
 * Register the Tag List Widget.
 *
 * @return void
 */
function tlw_register_widgets() {
	register_widget( 'TLW_Tag_List_Widget' );
}
add_action( 'widgets_init', 'tlw_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function tlw_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tlw_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function tlw_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'tlw_deactivate' );
