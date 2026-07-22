<?php
/**
 * Plugin Name:       Featured Rotator
 * Description:       Schedules a daily WP-Cron event to rotate the featured post ID stored as an option. Includes a [featured_post] shortcode to display the featured post title and link.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       featured-rotator
 *
 * @package Frrot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FRROT_VERSION', '1.0.0' );
define( 'FRROT_FILE', __FILE__ );
define( 'FRROT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function frrot_load_textdomain() {
	load_plugin_textdomain( 'featured-rotator', false, dirname( plugin_basename( FRROT_FILE ) ) . '/languages' );
}
add_action( 'init', 'frrot_load_textdomain' );

/**
 * Register the daily cron event.
 *
 * @return void
 */
function frrot_register_cron() {
	if ( ! wp_next_scheduled( 'featured_rotator_daily' ) ) {
		wp_schedule_event( time(), 'daily', 'featured_rotator_daily' );
	}
}
add_action( 'init', 'frrot_register_cron' );

/**
 * Rotate the featured post ID to a random published post.
 *
 * @return void
 */
function frrot_rotate_featured_post() {
	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		)
	);

	if ( empty( $posts ) ) {
		return;
	}

	$random_index = array_rand( $posts );
	$random_post  = $posts[ $random_index ];

	update_option( 'frrot_featured_post_id', $random_post->ID );
}
add_action( 'featured_rotator_daily', 'frrot_rotate_featured_post' );

/**
 * Shortcode handler for [featured_post].
 *
 * @return string
 */
function frrot_shortcode_featured_post() {
	$post_id = (int) get_option( 'frrot_featured_post_id', 0 );

	if ( ! $post_id ) {
		return esc_html__( 'No featured post set.', 'featured-rotator' );
	}

	$post = get_post( $post_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return esc_html__( 'Featured post is not published.', 'featured-rotator' );
	}

	$title = get_the_title( $post_id );
	$link  = get_permalink( $post_id );

	return '<a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a>';
}
add_shortcode( 'featured_post', 'frrot_shortcode_featured_post' );

/**
 * Activation hook.
 *
 * @return void
 */
function frrot_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'frrot_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function frrot_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'frrot_deactivate' );
