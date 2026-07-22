<?php
/**
 * Plugin Name:       Post Archiver
 * Description:       Schedule weekly archiving of old posts to private status with count tracking.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-archiver
 *
 * @package Parch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PARCH_VERSION', '1.0.0' );
define( 'PARCH_FILE', __FILE__ );
define( 'PARCH_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function parch_load_textdomain() {
	load_plugin_textdomain( 'post-archiver', false, dirname( plugin_basename( PARCH_FILE ) ) . '/languages' );
}
add_action( 'init', 'parch_load_textdomain' );

/**
 * Register the weekly cron event.
 *
 * @return void
 */
function parch_register_cron() {
	add_action( 'parch_weekly_archive', 'parch_weekly_archive' );
}
add_action( 'init', 'parch_register_cron' );

/**
 * Weekly archive posts older than 30 days to private status.
 *
 * @return void
 */
function parch_weekly_archive() {
	// Query for published posts older than 30 days.
	$posts = get_posts(
		array(
			'post_type'      => 'any',
			'post_status'    => 'publish',
			'date_query'     => array(
				array(
					'before' => '30 days ago',
				),
			),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	$count = count( $posts );

	// Update each post to private.
	foreach ( $posts as $post_id ) {
		wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => 'private',
			)
		);
	}

	// Update total archived count.
	$total_archived  = (int) get_option( 'parch_total_archived', 0 );
	$total_archived += $count;
	update_option( 'parch_total_archived', $total_archived );
}

/**
 * Activation hook. Schedule weekly cron event.
 *
 * @return void
 */
function parch_activate() {
	if ( ! wp_next_scheduled( 'parch_weekly_archive' ) ) {
		wp_schedule_event( time(), 'weekly', 'parch_weekly_archive' );
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'parch_activate' );

/**
 * Deactivation hook. Unschedules weekly cron event.
 *
 * @return void
 */
function parch_deactivate() {
	wp_clear_scheduled_hook( 'parch_weekly_archive' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'parch_deactivate' );
