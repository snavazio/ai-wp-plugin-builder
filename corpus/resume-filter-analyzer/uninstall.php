<?php
/**
 * Uninstall cleanup for Resume Filter Analyzer.
 *
 * @package Rfa
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/*
 * Clean up transient data.
 * The plugin uses transients for rate limiting (rfa_rate_*) and temporary analysis results (rfa_result_*).
 * These expire automatically, but we'll attempt to clean up any remaining ones.
 */

global $wpdb;

// Delete all transients with rfa_ prefix.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		$wpdb->esc_like( '_transient_rfa_' ) . '%',
		$wpdb->esc_like( '_transient_timeout_rfa_' ) . '%'
	)
);
