<?php
/**
 * Uninstall cleanup for Stats Snapshot.
 *
 * @package Stats
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'stats_snapshot_data' );
