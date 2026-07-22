<?php
/**
 * Plugin Name:       Call To Action Widget
 * Description:       A classic sidebar widget rendering a heading, a short message, and a button (label + URL) from its instance settings, all escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       call-to-action-widget
 *
 * @package Ctaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CTAW_VERSION', '1.0.0' );
define( 'CTAW_FILE', __FILE__ );
define( 'CTAW_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ctaw_load_textdomain() {
	load_plugin_textdomain( 'call-to-action-widget', false, dirname( plugin_basename( CTAW_FILE ) ) . '/languages' );
}
add_action( 'init', 'ctaw_load_textdomain' );

// Include widget class.
require_once CTAW_PATH . 'includes/widget.php';

/**
 * Register the Call To Action Widget.
 *
 * @return void
 */
function ctaw_register_widgets() {
	register_widget( 'CTAW_CTA_Widget' );
}
add_action( 'widgets_init', 'ctaw_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function ctaw_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ctaw_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function ctaw_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ctaw_deactivate' );
