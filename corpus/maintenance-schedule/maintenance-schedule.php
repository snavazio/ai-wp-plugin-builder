<?php
/**
 * Plugin Name:       Maintenance Schedule
 * Description:       A settings page with a scheduled-maintenance message and toggle; when on, show the escaped message to logged-out visitors. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       maintenance-schedule
 *
 * @package Mstg
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MSTG_VERSION', '1.0.0' );
define( 'MSTG_FILE', __FILE__ );
define( 'MSTG_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function mstg_load_textdomain() {
	load_plugin_textdomain( 'maintenance-schedule', false, dirname( plugin_basename( MSTG_FILE ) ) . '/languages' );
}
add_action( 'init', 'mstg_load_textdomain' );

// Include feature files.
require_once MSTG_PATH . 'includes/admin-page.php';

/**
 * Check if maintenance schedule is enabled and output the message if needed.
 *
 * @return void
 */
function mstg_maintenance_schedule() {
	if ( is_admin() ) {
		return;
	}

	$settings = get_option(
		'mstg_settings',
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
add_action( 'template_redirect', 'mstg_maintenance_schedule', 0 );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function mstg_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mstg_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function mstg_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'mstg_deactivate' );
