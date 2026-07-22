<?php
/**
 * Plugin Name:       Scheduled Cache Purge
 * Description:       Schedules daily cache purging via WP-Cron, deletes expired option-based cache entries, records last run time, and manages cron schedules on activation/deactivation.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       scheduled-cache-purge
 *
 * @package Scpc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SCPC_VERSION', '1.0.0' );
define( 'SCPC_FILE', __FILE__ );
define( 'SCPC_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function scpc_load_textdomain() {
	load_plugin_textdomain( 'scheduled-cache-purge', false, dirname( plugin_basename( SCPC_FILE ) ) . '/languages' );
}
add_action( 'init', 'scpc_load_textdomain' );

/**
 * Register the daily cron event.
 *
 * @return void
 */
function scpc_register_cron() {
	add_action( 'scp_daily_purge', 'scpc_daily_purge' );
}
add_action( 'init', 'scpc_register_cron' );

/**
 * Purge expired cache entries and record last run time.
 *
 * @return void
 */
function scpc_daily_purge() {
	global $wpdb;

	// Get all expiration options matching pattern.
	$expiration_keys = $wpdb->get_col(
		$wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s", 'scpc_cache_expiration_%' )
	);

	foreach ( $expiration_keys as $expiration_key ) {
		// Extract ID from key (e.g., 'scpc_cache_expiration_abc' → 'abc').
		$id        = substr( $expiration_key, strlen( 'scpc_cache_expiration_' ) );
		$cache_key = 'scpc_cache_' . $id;

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

	// Record last run time.
	update_option( 'scpc_last_purge', time() );
}

/**
 * Activation hook. Schedule daily cron event.
 *
 * @return void
 */
function scpc_activate() {
	if ( ! wp_next_scheduled( 'scp_daily_purge' ) ) {
		wp_schedule_event( time(), 'daily', 'scp_daily_purge' );
	}
}
register_activation_hook( __FILE__, 'scpc_activate' );

/**
 * Deactivation hook. Unschedules daily cron event.
 *
 * @return void
 */
function scpc_deactivate() {
	wp_clear_scheduled_hook( 'scp_daily_purge' );
}
register_deactivation_hook( __FILE__, 'scpc_deactivate' );
