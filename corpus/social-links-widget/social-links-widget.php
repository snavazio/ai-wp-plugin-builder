<?php
/**
 * Plugin Name:       Social Links Widget
 * Description:       Stores social profile URLs via a settings page and displays them as configurable sidebar links with proper escaping and sanitization.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       social-links-widget
 *
 * @package Slwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SLWGT_VERSION', '1.0.0' );
define( 'SLWGT_FILE', __FILE__ );
define( 'SLWGT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function slwgt_load_textdomain() {
	load_plugin_textdomain( 'social-links-widget', false, dirname( plugin_basename( SLWGT_FILE ) ) . '/languages' );
}
add_action( 'init', 'slwgt_load_textdomain' );

// Include feature files.
require_once SLWGT_PATH . 'includes/admin-page.php';
require_once SLWGT_PATH . 'includes/widget.php';

/**
 * Register the Social Links Widget.
 *
 * @return void
 */
function slwgt_register_widgets() {
	register_widget( 'SLWGT_Social_Links_Widget' );
}
add_action( 'widgets_init', 'slwgt_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function slwgt_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'slwgt_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function slwgt_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'slwgt_deactivate' );
