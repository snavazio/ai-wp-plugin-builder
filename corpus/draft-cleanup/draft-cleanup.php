<?php
/**
 * Plugin Name:       Draft Cleanup
 * Description:       Automatically trashes auto-draft posts older than 7 days via weekly cron, records counts in options.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       draft-cleanup
 *
 * @package Dctc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DCTC_VERSION', '1.0.0' );
define( 'DCTC_FILE', __FILE__ );
define( 'DCTC_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function dctc_load_textdomain() {
	load_plugin_textdomain( 'draft-cleanup', false, dirname( plugin_basename( DCTC_FILE ) ) . '/languages' );
}
add_action( 'init', 'dctc_load_textdomain' );

/**
 * Register the weekly cron event.
 *
 * @return void
 */
function dctc_register_cron() {
	add_action( 'draft_cleanup_weekly', 'dctc_cleanup_auto_drafts' );
}
add_action( 'init', 'dctc_register_cron' );

/**
 * Clean up auto-draft posts older than 7 days.
 *
 * @return void
 */
function dctc_cleanup_auto_drafts() {
	// Get auto-draft posts older than 7 days.
	$auto_drafts = get_posts(
		array(
			'post_type'      => 'any',
			'post_status'    => 'auto-draft',
			'date_query'     => array(
				array(
					'before' => '7 days ago',
				),
			),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	$count = count( $auto_drafts );

	// Trash each post.
	foreach ( $auto_drafts as $post_id ) {
		wp_trash_post( $post_id );
	}

	// Record total count in option.
	$total_trashed  = (int) get_option( 'dctc_total_trashed', 0 );
	$total_trashed += $count;
	update_option( 'dctc_total_trashed', $total_trashed );
}

/**
 * Activation hook. Schedule weekly cron event.
 *
 * @return void
 */
function dctc_activate() {
	if ( ! wp_next_scheduled( 'draft_cleanup_weekly' ) ) {
		wp_schedule_event( time(), 'weekly', 'draft_cleanup_weekly' );
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dctc_activate' );

/**
 * Deactivation hook. Unschedules weekly cron event.
 *
 * @return void
 */
function dctc_deactivate() {
	wp_clear_scheduled_hook( 'draft_cleanup_weekly' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dctc_deactivate' );
