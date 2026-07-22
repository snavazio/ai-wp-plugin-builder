<?php
/**
 * Plugin Name:       Author Bio Widget
 * Description:       Displays the post author's name and description in the sidebar on single posts with a customizable heading.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       author-bio-widget
 *
 * @package Abio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ABIO_VERSION', '1.0.0' );
define( 'ABIO_FILE', __FILE__ );
define( 'ABIO_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function abio_load_textdomain() {
	load_plugin_textdomain( 'author-bio-widget', false, dirname( plugin_basename( ABIO_FILE ) ) . '/languages' );
}
add_action( 'init', 'abio_load_textdomain' );

// Include widget class.
require_once ABIO_PATH . 'includes/widget.php';

/**
 * Register the Author Bio Widget.
 *
 * @return void
 */
function abio_register_widgets() {
	register_widget( 'ABIO_Author_Bio_Widget' );
}
add_action( 'widgets_init', 'abio_register_widgets' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function abio_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'abio_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function abio_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'abio_deactivate' );
