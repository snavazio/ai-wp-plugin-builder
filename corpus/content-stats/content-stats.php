<?php
/**
 * Plugin Name:       Content Stats
 * Description:       An admin dashboard widget showing counts of published posts, pages, and comments, visible only to users who can edit posts.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       content-stats
 *
 * @package Cstw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CSTW_VERSION', '1.0.0' );
define( 'CSTW_FILE', __FILE__ );
define( 'CSTW_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cstw_load_textdomain() {
	load_plugin_textdomain( 'content-stats', false, dirname( plugin_basename( CSTW_FILE ) ) . '/languages' );
}
add_action( 'init', 'cstw_load_textdomain' );

// Include the dashboard widget.
require_once CSTW_PATH . 'includes/widget.php';

/**
 * Register the Content Stats dashboard widget.
 *
 * @return void
 */
function cstw_register_dashboard_widget() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	wp_add_dashboard_widget(
		'cstw_content_stats',
		__( 'Content Stats', 'content-stats' ),
		'cstw_dashboard_widget_content'
	);
}
add_action( 'wp_dashboard_setup', 'cstw_register_dashboard_widget' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cstw_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cstw_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cstw_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cstw_deactivate' );
