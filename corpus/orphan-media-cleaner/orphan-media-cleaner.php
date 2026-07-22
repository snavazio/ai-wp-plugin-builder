<?php
/**
 * Plugin Name:       Orphan Media Cleaner
 * Description:       Counts unattached media items weekly and stores tally in WordPress options without deletion.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       orphan-media-cleaner
 *
 * @package Omclean
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OMCLEAN_VERSION', '1.0.0' );
define( 'OMCLEAN_FILE', __FILE__ );
define( 'OMCLEAN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function omclean_load_textdomain() {
	load_plugin_textdomain( 'orphan-media-cleaner', false, dirname( plugin_basename( OMCLEAN_FILE ) ) . '/languages' );
}
add_action( 'init', 'omclean_load_textdomain' );

/**
 * Register the weekly cron event.
 *
 * @return void
 */
function omclean_register_cron() {
	add_action( 'omclean_weekly_cleanup', 'omclean_weekly_cleanup' );
}
add_action( 'init', 'omclean_register_cron' );

/**
 * Count unattached media items and store tally in option.
 *
 * @return void
 */
function omclean_weekly_cleanup() {
	// Query all media attachments.
	$attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	$unattached_count = 0;
	foreach ( $attachments as $attachment_id ) {
		$parent_id = get_post_field( 'post_parent', $attachment_id );
		// Count if parent is 0 (unattached) or parent post doesn't exist.
		if ( 0 === $parent_id || ! get_post( $parent_id ) ) {
			++$unattached_count;
		}
	}

	// Store current count in option.
	update_option( 'omclean_media_count', $unattached_count );
}

/**
 * Activation hook. Schedule weekly cron event.
 *
 * @return void
 */
function omclean_activate() {
	if ( ! wp_next_scheduled( 'omclean_weekly_cleanup' ) ) {
		wp_schedule_event( time(), 'weekly', 'omclean_weekly_cleanup' );
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'omclean_activate' );

/**
 * Deactivation hook. Unschedules weekly cron event.
 *
 * @return void
 */
function omclean_deactivate() {
	wp_clear_scheduled_hook( 'omclean_weekly_cleanup' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'omclean_deactivate' );
