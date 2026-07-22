<?php
/**
 * Plugin Name:       Maintenance Mode
 * Description:       Enable maintenance mode with a custom message for non-logged-in visitors.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       maintenance-mode
 *
 * @package Mmtm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MMTM_VERSION', '1.0.0' );
define( 'MMTM_FILE', __FILE__ );
define( 'MMTM_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function mmtm_load_textdomain() {
	load_plugin_textdomain( 'maintenance-mode', false, dirname( plugin_basename( MMTM_FILE ) ) . '/languages' );
}
add_action( 'init', 'mmtm_load_textdomain' );

// Include feature files.
require_once MMTM_PATH . 'includes/admin-page.php';

/**
 * Check if maintenance mode is enabled and output the message if needed.
 *
 * @return void
 */
function mmtm_maintenance_mode() {
	if ( is_admin() ) {
		return;
	}

	$settings = get_option(
		'mmtm_settings',
		array(
			'enabled' => false,
			'message' => '',
		)
	);
	if ( ! $settings['enabled'] ) {
		return;
	}

	// Escape message for safe output.
	wp_die( esc_html( $settings['message'] ) );
}
add_action( 'template_redirect', 'mmtm_maintenance_mode', 0 );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function mmtm_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mmtm_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function mmtm_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'mmtm_deactivate' );
