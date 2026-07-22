<?php
/**
 * Plugin Name:       Weekly Digest
 * Description:       Schedules a weekly cron event to count posts published in the last 7 days and displays the count via a shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       weekly-digest
 *
 * @package Wkdig
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WKDIG_VERSION', '1.0.0' );
define( 'WKDIG_FILE', __FILE__ );
define( 'WKDIG_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function wkdig_load_textdomain() {
	load_plugin_textdomain( 'weekly-digest', false, dirname( plugin_basename( WKDIG_FILE ) ) . '/languages' );
}
add_action( 'init', 'wkdig_load_textdomain' );

/**
 * Count posts published in the last 7 days and store the count.
 *
 * @return void
 */
function wkdig_weekly_digest_count() {
	$date_query = array(
		array(
			'after'     => gmdate( 'Y-m-d', strtotime( '-7 days' ) ),
			'inclusive' => true,
		),
	);

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'date_query'     => $date_query,
		'fields'         => 'ids',
		'posts_per_page' => -1,
	);

	$posts = get_posts( $args );
	$count = count( $posts );

	update_option( 'wkdig_weekly_post_count', $count );
}

/**
 * Register the weekly cron event.
 *
 * @return void
 */
function wkdig_register_cron() {
	if ( ! wp_next_scheduled( 'wkdig_weekly_digest' ) ) {
		wp_schedule_event( time(), 'weekly', 'wkdig_weekly_digest' );
	}
}
add_action( 'init', 'wkdig_register_cron' );

/**
 * Handle the weekly digest cron event.
 *
 * @return void
 */
function wkdig_handle_cron() {
	wkdig_weekly_digest_count();
}
add_action( 'wkdig_weekly_digest', 'wkdig_handle_cron' );

/**
 * Shortcode handler for [weekly_digest].
 *
 * @return string
 */
function wkdig_shortcode_weekly_digest() {
	$count = get_option( 'wkdig_weekly_post_count', 0 );

	if ( 0 === $count ) {
		return esc_html__( 'No posts published in the last 7 days.', 'weekly-digest' );
	}

	return esc_html( $count );
}
add_shortcode( 'weekly_digest', 'wkdig_shortcode_weekly_digest' );

/**
 * Activation hook.
 *
 * @return void
 */
function wkdig_activate() {
	// No rewrite rules to flush.
}
register_activation_hook( __FILE__, 'wkdig_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function wkdig_deactivate() {
	// No rewrite rules to flush.
}
register_deactivation_hook( __FILE__, 'wkdig_deactivate' );
