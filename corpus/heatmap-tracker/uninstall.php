<?php
/**
 * Uninstall cleanup for Heatmap Tracker.
 *
 * @package Hmtrk
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

if ( get_option( 'hmtrk_delete_on_uninstall' ) ) {
	global $wpdb;
	$hmtrk_table = $wpdb->prefix . 'hmtrk_events';
	$wpdb->query( "DROP TABLE IF EXISTS {$hmtrk_table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders

	delete_option( 'hmtrk_tracked_posts' );
	delete_option( 'hmtrk_delete_on_uninstall' );
	delete_option( 'hmtrk_db_version' );
}
