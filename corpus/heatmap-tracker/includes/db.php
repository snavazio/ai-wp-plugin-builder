<?php
/**
 * Database schema and helpers.
 *
 * @package Hmtrk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HMTRK_DB_SCHEMA', '1' );

/**
 * Get the events table name.
 *
 * @return string
 */
function hmtrk_table() {
	global $wpdb;
	return $wpdb->prefix . 'hmtrk_events';
}

/**
 * Create or update the events table with dbDelta.
 *
 * @return void
 */
function hmtrk_install_table() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table   = hmtrk_table();
	$charset = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		post_id bigint(20) unsigned NOT NULL,
		event_type varchar(10) NOT NULL,
		pos_x smallint(5) unsigned NOT NULL DEFAULT 0,
		pos_y int(10) unsigned NOT NULL DEFAULT 0,
		scroll_depth tinyint(3) unsigned NOT NULL DEFAULT 0,
		device varchar(10) NOT NULL,
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY post_device (post_id,device,event_type)
	) {$charset};";

	dbDelta( $sql );
	update_option( 'hmtrk_db_version', HMTRK_DB_SCHEMA );
}

/**
 * Install the table when the stored schema version is missing or old.
 *
 * @return void
 */
function hmtrk_maybe_upgrade() {
	if ( get_option( 'hmtrk_db_version' ) !== HMTRK_DB_SCHEMA ) {
		hmtrk_install_table();
	}
}
add_action( 'plugins_loaded', 'hmtrk_maybe_upgrade' );

/**
 * Get the list of tracked post IDs.
 *
 * @return int[]
 */
function hmtrk_get_tracked_ids() {
	$ids = get_option( 'hmtrk_tracked_posts', array() );
	return is_array( $ids ) ? array_map( 'absint', $ids ) : array();
}

/**
 * Whether a post ID is tracked.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function hmtrk_is_tracked( $post_id ) {
	return $post_id > 0 && in_array( (int) $post_id, hmtrk_get_tracked_ids(), true );
}
