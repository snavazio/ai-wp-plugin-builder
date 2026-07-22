<?php
/**
 * Plugin Name:       Mini CTA Widget
 * Description:       A classic sidebar widget rendering a heading, message, and a single button (label + URL) from instance settings, all escaped.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mini-cta-widget
 *
 * @package Mctw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MCTW_VERSION', '1.0.0' );
define( 'MCTW_FILE', __FILE__ );
define( 'MCTW_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function mctw_load_textdomain() {
	load_plugin_textdomain( 'mini-cta-widget', false, dirname( plugin_basename( MCTW_FILE ) ) . '/languages' );
}
add_action( 'init', 'mctw_load_textdomain' );

// Include widget class.
require_once MCTW_PATH . 'includes/widget.php';

/**
 * Register the Mini CTA Widget.
 *
 * @return void
 */
function mctw_register_widgets() {
	register_widget( 'MCTW_CTA_Widget' );
}
add_action( 'widgets_init', 'mctw_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function mctw_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mctw_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function mctw_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'mctw_deactivate' );
