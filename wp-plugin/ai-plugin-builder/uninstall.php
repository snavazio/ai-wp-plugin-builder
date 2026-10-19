<?php
/**
 * Uninstall cleanup for AI Plugin Builder — remove stored settings.
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

foreach ( array( 'aiwpb_backend_url', 'aiwpb_api_key', 'aiwpb_default_engine', 'aiwpb_local_model', 'aiwpb_platforms', 'aiwpb_default_platform', 'aiwpb_edit_platform', 'aiwpb_github_owner', 'aiwpb_github_token', 'aiwpb_github_private' ) as $aiwpb_option ) {
	delete_option( $aiwpb_option );
}

// Rollback backups: one option + one .zip per plugin that was updated in place.
global $wpdb;
$aiwpb_backup_options = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like( 'aiwpb_backup_' ) . '%' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-off cleanup on uninstall.
foreach ( (array) $aiwpb_backup_options as $aiwpb_option ) {
	delete_option( $aiwpb_option );
}
$aiwpb_uploads = wp_upload_dir();
$aiwpb_backups = trailingslashit( $aiwpb_uploads['basedir'] ) . 'aiwpb-backups';
if ( is_dir( $aiwpb_backups ) ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	if ( WP_Filesystem() ) {
		global $wp_filesystem;
		$wp_filesystem->rmdir( $aiwpb_backups, true );
	}
}
