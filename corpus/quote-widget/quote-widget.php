<?php
/**
 * Plugin Name:       Quote Widget
 * Description:       Classic sidebar widget for displaying quotes with configurable title and author, featuring full output escaping and input sanitization.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       quote-widget
 *
 * @package Qwid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'QWID_VERSION', '1.0.0' );
define( 'QWID_FILE', __FILE__ );
define( 'QWID_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function qwid_load_textdomain() {
	load_plugin_textdomain( 'quote-widget', false, dirname( plugin_basename( QWID_FILE ) ) . '/languages' );
}
add_action( 'init', 'qwid_load_textdomain' );

// Include widget class.
require_once QWID_PATH . 'includes/widget.php';

/**
 * Register the Quote Widget.
 *
 * @return void
 */
function qwid_register_widgets() {
	register_widget( 'QWID_Quote_Widget' );
}
add_action( 'widgets_init', 'qwid_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function qwid_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'qwid_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function qwid_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'qwid_deactivate' );
