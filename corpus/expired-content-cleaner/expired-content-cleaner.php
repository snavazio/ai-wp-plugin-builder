<?php
/**
 * Plugin Name:       Expired Content Cleaner
 * Description:       Automatically moves expired published posts to draft status based on expiry date meta, records counts via WP-Cron.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       expired-content-cleaner
 *
 * @package Expcl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EXPCL_VERSION', '1.0.0' );
define( 'EXPCL_FILE', __FILE__ );
define( 'EXPCL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function expcl_load_textdomain() {
	load_plugin_textdomain( 'expired-content-cleaner', false, dirname( plugin_basename( EXPCL_FILE ) ) . '/languages' );
}
add_action( 'init', 'expcl_load_textdomain' );

/**
 * Register the weekly cron event.
 *
 * @return void
 */
function expcl_register_cron() {
	add_action( 'expired_content_cleaner_cron', 'expcl_clean_expired_posts' );
}
add_action( 'init', 'expcl_register_cron' );

/**
 * Clean expired published posts by moving them to draft status.
 *
 * @return void
 */
function expcl_clean_expired_posts() {
	// Query for expired published posts.
	$posts = get_posts(
		array(
			'post_type'      => 'any',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'expcl_expiry_date',
					'value'   => time(),
					'compare' => '<',
					'type'    => 'NUMERIC',
				),
			),
			'fields'         => 'ids',
		)
	);

	$count = count( $posts );

	// Move each expired post to draft.
	foreach ( $posts as $post_id ) {
		wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => 'draft',
			)
		);
	}

	// Record total count in option.
	$current_count = (int) get_option( 'expired_content_cleaner_count', 0 );
	update_option( 'expired_content_cleaner_count', $current_count + $count );
}

/**
 * Activation hook. Schedule weekly cron event.
 *
 * @return void
 */
function expcl_activate() {
	if ( ! wp_next_scheduled( 'expired_content_cleaner_cron' ) ) {
		wp_schedule_event( time(), 'weekly', 'expired_content_cleaner_cron' );
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'expcl_activate' );

/**
 * Deactivation hook. Unschedules weekly cron event.
 *
 * @return void
 */
function expcl_deactivate() {
	wp_clear_scheduled_hook( 'expired_content_cleaner_cron' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'expcl_deactivate' );
