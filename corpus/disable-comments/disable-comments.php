<?php
/**
 * Plugin Name:       Disable Comments
 * Description:       A settings page with a toggle to disable comments site-wide. When enabled, closes comments on all post types and hides existing comments.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       disable-comments
 *
 * @package Dcmn
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DCMN_VERSION', '1.0.0' );
define( 'DCMN_FILE', __FILE__ );
define( 'DCMN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function dcmn_load_textdomain() {
	load_plugin_textdomain( 'disable-comments', false, dirname( plugin_basename( DCMN_FILE ) ) . '/languages' );
}
add_action( 'init', 'dcmn_load_textdomain' );

// Include feature files.
require_once DCMN_PATH . 'includes/admin-page.php';

/**
 * Register the Disable Comments settings page.
 *
 * @return void
 */
function dcmn_register_admin_page() {
	add_options_page(
		__( 'Disable Comments', 'disable-comments' ),
		__( 'Disable Comments', 'disable-comments' ),
		'manage_options',
		'disable-comments-settings',
		'dcmn_admin_page_callback'
	);
}
add_action( 'admin_menu', 'dcmn_register_admin_page' );

/**
 * Setup frontend filters for comment disabling.
 *
 * @return void
 */
function dcmn_setup_frontend_filters() {
	if ( get_option( 'dcmn_comments_enabled', false ) ) {
		add_filter( 'comments_open', 'dcmn_comments_open', 10, 2 );
		add_filter( 'comments_array', 'dcmn_hide_comments', 10, 2 );
	}
}
add_action( 'init', 'dcmn_setup_frontend_filters' );

/**
 * Disable comments for all posts.
 *
 * @param bool $open   Whether comments are open.
 * @param int  $post_id Post ID.
 * @return bool Always false.
 */
function dcmn_comments_open( $open, $post_id ) {
	return false;
}

/**
 * Hide all comments from display.
 *
 * @param array $comments Comments array.
 * @param int   $post_id  Post ID.
 * @return array Empty array.
 */
function dcmn_hide_comments( $comments, $post_id ) {
	return array();
}
