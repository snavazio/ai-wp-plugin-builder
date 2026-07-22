<?php
/**
 * Plugin Name:       Popular Posts Widget
 * Description:       A classic sidebar widget listing the N most-commented published posts with configurable count and heading.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       popular-posts-widget
 *
 * @package Ppwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PPWGT_VERSION', '1.0.0' );
define( 'PPWGT_FILE', __FILE__ );
define( 'PPWGT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ppwgt_load_textdomain() {
	load_plugin_textdomain( 'popular-posts-widget', false, dirname( plugin_basename( PPWGT_FILE ) ) . '/languages' );
}
add_action( 'init', 'ppwgt_load_textdomain' );

// Include widget class.
require_once PPWGT_PATH . 'includes/widget.php';

/**
 * Register the Popular Posts Widget.
 *
 * @return void
 */
function ppwgt_register_widgets() {
	register_widget( 'PPWGT_Popular_Posts_Widget' );
}
add_action( 'widgets_init', 'ppwgt_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function ppwgt_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ppwgt_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function ppwgt_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ppwgt_deactivate' );
