<?php
/**
 * Plugin Name:       Featured Post Widget
 * Description:       A classic sidebar widget displaying the title and excerpt of a selected published post with configurable title.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       featured-post-widget
 *
 * @package Fpw1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FPW1_VERSION', '1.0.0' );
define( 'FPW1_FILE', __FILE__ );
define( 'FPW1_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function fpw1_load_textdomain() {
	load_plugin_textdomain( 'featured-post-widget', false, dirname( plugin_basename( FPW1_FILE ) ) . '/languages' );
}
add_action( 'init', 'fpw1_load_textdomain' );

// Include widget class.
require_once FPW1_PATH . 'includes/widget.php';

/**
 * Register the Featured Post Widget.
 *
 * @return void
 */
function fpw1_register_widgets() {
	register_widget( 'Fpw1_Featured_Post_Widget' );
}
add_action( 'widgets_init', 'fpw1_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function fpw1_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'fpw1_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function fpw1_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'fpw1_deactivate' );
