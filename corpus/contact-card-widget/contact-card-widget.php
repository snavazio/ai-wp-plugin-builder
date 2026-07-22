<?php
/**
 * Plugin Name:       Contact Card Widget
 * Description:       A classic sidebar widget rendering phone, email, and address from its instance settings, each escaped, with a configurable heading.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       contact-card-widget
 *
 * @package Ccw1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CCW1_VERSION', '1.0.0' );
define( 'CCW1_FILE', __FILE__ );
define( 'CCW1_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ccw1_load_textdomain() {
	load_plugin_textdomain( 'contact-card-widget', false, dirname( plugin_basename( CCW1_FILE ) ) . '/languages' );
}
add_action( 'init', 'ccw1_load_textdomain' );

// Include widget class.
require_once CCW1_PATH . 'includes/widget.php';

/**
 * Register the Contact Card Widget.
 *
 * @return void
 */
function ccw1_register_widgets() {
	register_widget( 'CCW1_Contact_Card_Widget' );
}
add_action( 'widgets_init', 'ccw1_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function ccw1_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ccw1_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function ccw1_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ccw1_deactivate' );
