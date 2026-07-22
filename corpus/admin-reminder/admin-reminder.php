<?php
/**
 * Plugin Name:       Admin Reminder
 * Description:       A settings page with a dismissible admin notice for editors.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       admin-reminder
 *
 * @package Arrem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARREM_VERSION', '1.0.0' );
define( 'ARREM_FILE', __FILE__ );
define( 'ARREM_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function arrem_load_textdomain() {
	load_plugin_textdomain( 'admin-reminder', false, dirname( plugin_basename( ARREM_FILE ) ) . '/languages' );
}
add_action( 'init', 'arrem_load_textdomain' );

// Include feature files.
require_once ARREM_PATH . 'includes/admin-page.php';

/**
 * Register the Admin Reminder settings page.
 *
 * @return void
 */
function arrem_register_admin_page() {
	add_options_page(
		__( 'Admin Reminder', 'admin-reminder' ),
		__( 'Admin Reminder', 'admin-reminder' ),
		'edit_posts',
		'admin-reminder',
		'arrem_admin_page_callback'
	);
}
add_action( 'admin_menu', 'arrem_register_admin_page' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function arrem_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'arrem_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function arrem_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'arrem_deactivate' );
