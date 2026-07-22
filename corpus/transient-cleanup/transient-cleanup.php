<?php
/**
 * Plugin Name:       Transient Cleanup
 * Description:       Automatically cleans up expired cache entries stored as options with a scheduled daily task.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       transient-cleanup
 *
 * @package Tcleanup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TCLEANUP_VERSION', '1.0.0' );
define( 'TCLEANUP_FILE', __FILE__ );
define( 'TCLEANUP_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function tcleanup_load_textdomain() {
	load_plugin_textdomain( 'transient-cleanup', false, dirname( plugin_basename( TCLEANUP_FILE ) ) . '/languages' );
}
add_action( 'init', 'tcleanup_load_textdomain' );

/**
 * Register the daily cron event.
 *
 * @return void
 */
function tcleanup_register_cron() {
	add_action( 'tcleanup_daily_cron', 'tcleanup_cleanup_expired_transients' );
}
add_action( 'init', 'tcleanup_register_cron' );

/**
 * Clean up expired transient cache entries.
 *
 * @return void
 */
function tcleanup_cleanup_expired_transients() {
	global $wpdb;

	// Get all expiration options matching pattern.
	$expiration_keys = $wpdb->get_col(
		$wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s", 'tcleanup_transient_expiration_%' )
	);

	foreach ( $expiration_keys as $expiration_key ) {
		// Extract ID from key (e.g., 'tcleanup_transient_expiration_abc' → 'abc').
		$id        = substr( $expiration_key, strlen( 'tcleanup_transient_expiration_' ) );
		$cache_key = 'tcleanup_transient_' . $id;

		// Get expiration timestamp.
		$expiration = get_option( $expiration_key );
		if ( false === $expiration || ! is_numeric( $expiration ) ) {
			continue;
		}

		// Delete if expired.
		if ( time() > $expiration ) {
			delete_option( $cache_key );
			delete_option( $expiration_key );
		}
	}
}

/**
 * Activation hook. Schedule daily cron event.
 *
 * @return void
 */
function tcleanup_activate() {
	if ( ! wp_next_scheduled( 'tcleanup_daily_cron' ) ) {
		wp_schedule_event( time(), 'daily', 'tcleanup_daily_cron' );
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tcleanup_activate' );

/**
 * Deactivation hook. Unschedules daily cron event.
 *
 * @return void
 */
function tcleanup_deactivate() {
	wp_clear_scheduled_hook( 'tcleanup_daily_cron' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'tcleanup_deactivate' );
