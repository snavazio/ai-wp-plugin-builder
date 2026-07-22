<?php
/**
 * Plugin Name:       Recent Comments Widget
 * Description:       A classic sidebar widget listing the N most recent approved comments with escaped author and excerpt and a configurable count.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       recent-comments-widget
 *
 * @package Rcws
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RCWS_VERSION', '1.0.0' );
define( 'RCWS_FILE', __FILE__ );
define( 'RCWS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rcws_load_textdomain() {
	load_plugin_textdomain( 'recent-comments-widget', false, dirname( plugin_basename( RCWS_FILE ) ) . '/languages' );
}
add_action( 'init', 'rcws_load_textdomain' );

// Include widget class.
require_once RCWS_PATH . 'includes/widget.php';

/**
 * Register the Recent Comments Widget.
 *
 * @return void
 */
function rcws_register_widgets() {
	register_widget( 'RCWS_Recent_Comments_Widget' );
}
add_action( 'widgets_init', 'rcws_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function rcws_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rcws_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rcws_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rcws_deactivate' );
