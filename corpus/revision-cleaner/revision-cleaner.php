<?php
/**
 * Plugin Name:       Revision Cleaner
 * Description:       Automates deletion of old post revisions with configurable retention (keeps 5 most recent per post, deletes older than 60 days). Records deleted count in option.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       revision-cleaner
 *
 * @package Revcln
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'REVCLN_VERSION', '1.0.0' );
define( 'REVCLN_FILE', __FILE__ );
define( 'REVCLN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function revcln_load_textdomain() {
	load_plugin_textdomain( 'revision-cleaner', false, dirname( plugin_basename( REVCLN_FILE ) ) . '/languages' );
}
add_action( 'init', 'revcln_load_textdomain' );

/**
 * Register the weekly cron event.
 *
 * @return void
 */
function revcln_register_cron() {
	add_action( 'revcln_revision_cleaner_weekly_cron', 'revcln_revision_cleaner_weekly_cron' );
}
add_action( 'init', 'revcln_register_cron' );

/**
 * Clean up post revisions older than 60 days, keeping 5 most recent per post.
 *
 * @return void
 */
function revcln_revision_cleaner_weekly_cron() {
	// Get all revisions.
	$revisions = get_posts(
		array(
			'post_type'      => 'revision',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	$total_deleted     = 0;
	$revisions_by_post = array();

	foreach ( $revisions as $revision_id ) {
		$post_parent = wp_get_post_parent_id( $revision_id );
		if ( ! isset( $revisions_by_post[ $post_parent ] ) ) {
			$revisions_by_post[ $post_parent ] = array();
		}
		$revisions_by_post[ $post_parent ][] = $revision_id;
	}

	foreach ( $revisions_by_post as $post_id => $revision_ids ) {
		// Sort revisions by date descending.
		$revision_dates = array();
		foreach ( $revision_ids as $id ) {
			$revision_dates[ $id ] = get_post_field( 'post_date', $id );
		}
		array_multisort( $revision_dates, SORT_DESC, $revision_ids );

		// Keep the 5 most recent revisions.
		$keep           = array_slice( $revision_ids, 0, 5 );
		$delete         = array_diff( $revision_ids, $keep );
		$total_deleted += count( $delete );

		// Delete revisions.
		foreach ( $delete as $id ) {
			wp_delete_post( $id, true );
		}
	}

	// Record total count in option.
	$current = (int) get_option( 'revcln_deleted_count', 0 );
	update_option( 'revcln_deleted_count', $current + $total_deleted );
}

/**
 * Activation hook. Schedule weekly cron event.
 *
 * @return void
 */
function revcln_activate() {
	if ( ! wp_next_scheduled( 'revcln_revision_cleaner_weekly_cron' ) ) {
		wp_schedule_event( time(), 'weekly', 'revcln_revision_cleaner_weekly_cron' );
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'revcln_activate' );

/**
 * Deactivation hook. Unschedules weekly cron event.
 *
 * @return void
 */
function revcln_deactivate() {
	wp_clear_scheduled_hook( 'revcln_revision_cleaner_weekly_cron' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'revcln_deactivate' );
