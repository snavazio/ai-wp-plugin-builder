<?php
/**
 * Plugin Name:       Recent Products Widget
 * Description:       A sidebar widget listing the N most recent published posts of a 'product' post type if present (else posts), with escaped titles and configurable count.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       recent-products-widget
 *
 * @package Rpwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RPWGT_VERSION', '1.0.0' );
define( 'RPWGT_FILE', __FILE__ );
define( 'RPWGT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rpwgt_load_textdomain() {
	load_plugin_textdomain( 'recent-products-widget', false, dirname( plugin_basename( RPWGT_FILE ) ) . '/languages' );
}
add_action( 'init', 'rpwgt_load_textdomain' );

// Include widget class.
require_once RPWGT_PATH . 'includes/widget.php';

/**
 * Register the Recent Products Widget.
 *
 * @return void
 */
function rpwgt_register_widgets() {
	register_widget( 'Rpwgt_Recent_Products_Widget' );
}
add_action( 'widgets_init', 'rpwgt_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function rpwgt_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rpwgt_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rpwgt_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rpwgt_deactivate' );
