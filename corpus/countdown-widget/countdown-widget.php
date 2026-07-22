<?php
/**
 * Plugin Name:       Countdown Widget
 * Description:       A classic sidebar widget with a target-date instance setting (sanitized) that renders the number of days remaining, escaped, with a configurable title. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       countdown-widget
 *
 * @package Cdwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CDWGT_VERSION', '1.0.0' );
define( 'CDWGT_FILE', __FILE__ );
define( 'CDWGT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cdwgt_load_textdomain() {
	load_plugin_textdomain( 'countdown-widget', false, dirname( plugin_basename( CDWGT_FILE ) ) . '/languages' );
}
add_action( 'init', 'cdwgt_load_textdomain' );

// Include widget class.
require_once CDWGT_PATH . 'includes/widget.php';

/**
 * Register the Countdown Widget.
 *
 * @return void
 */
function cdwgt_register_widgets() {
	register_widget( 'CDWGT_Widget' );
}
add_action( 'widgets_init', 'cdwgt_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cdwgt_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cdwgt_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cdwgt_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cdwgt_deactivate' );
