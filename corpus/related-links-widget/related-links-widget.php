<?php
/**
 * Plugin Name:       Related Links Widget
 * Description:       A classic sidebar widget rendering a configurable list of label+URL link pairs from its instance settings, all escaped.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       related-links-widget
 *
 * @package Rlwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RLWGT_VERSION', '1.0.0' );
define( 'RLWGT_FILE', __FILE__ );
define( 'RLWGT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rlwgt_load_textdomain() {
	load_plugin_textdomain( 'related-links-widget', false, dirname( plugin_basename( RLWGT_FILE ) ) . '/languages' );
}
add_action( 'init', 'rlwgt_load_textdomain' );

// Include widget class.
require_once RLWGT_PATH . 'includes/widget.php';

/**
 * Register the Related Links Widget.
 *
 * @return void
 */
function rlwgt_register_widgets() {
	register_widget( 'Rlwgt_Related_Links_Widget' );
}
add_action( 'widgets_init', 'rlwgt_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function rlwgt_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rlwgt_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rlwgt_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rlwgt_deactivate' );
