<?php
/**
 * Plugin Name:       Link Audit Log
 * Description:       Schedule a daily WP-Cron event that counts published posts containing external links and stores the tally with a timestamp in an option (keep last 14). Register/clear on activation/deactivation. A [link_audit] shortcode shows the latest, escaped.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       link-audit-log
 *
 * @package Lalx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LALX_VERSION', '1.0.0' );
define( 'LALX_FILE', __FILE__ );
define( 'LALX_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function lalx_load_textdomain() {
	load_plugin_textdomain( 'link-audit-log', false, dirname( plugin_basename( LALX_FILE ) ) . '/languages' );
}
add_action( 'init', 'lalx_load_textdomain' );

/**
 * Store a daily audit snapshot of the count of published posts containing external links.
 *
 * @return void
 */
function lalx_store_audit_data() {
	$home_url    = get_home_url();
	$home_domain = parse_url( $home_url, PHP_URL_HOST );
	if ( ! $home_domain ) {
		return;
	}
	$home_domain = strtolower( $home_domain );
	$home_domain = str_replace( 'www.', '', $home_domain );

	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		)
	);

	$external_posts_count = 0;
	foreach ( $posts as $post ) {
		$content = $post->post_content;
		if ( preg_match_all( '/<a\s[^>]*href="([^"]*)"/i', $content, $matches ) ) {
			foreach ( $matches[1] as $link ) {
				$link = trim( $link );
				if ( empty( $link ) ) {
					continue;
				}

				$parsed_link = parse_url( $link );
				if ( ! isset( $parsed_link['scheme'] ) || ! in_array( $parsed_link['scheme'], array( 'http', 'https' ), true ) ) {
					continue;
				}

				$link_domain = $parsed_link['host'] ?? '';
				if ( ! $link_domain ) {
					continue;
				}
				$link_domain = strtolower( $link_domain );
				$link_domain = str_replace( 'www.', '', $link_domain );

				if ( $link_domain === $home_domain ) {
					continue;
				} else {
					++$external_posts_count;
					break;
				}
			}
		}
	}

	$timestamp = current_time( 'mysql' );
	$data      = get_option( 'lalx_audit_log', array() );
	$new_entry = array(
		'timestamp' => $timestamp,
		'count'     => $external_posts_count,
	);

	array_unshift( $data, $new_entry );
	if ( count( $data ) > 14 ) {
		$data = array_slice( $data, 0, 14 );
	}

	update_option( 'lalx_audit_log', $data );
}

/**
 * Register the daily cron event.
 *
 * @return void
 */
function lalx_register_cron() {
	if ( ! wp_next_scheduled( 'lalx_daily_audit' ) ) {
		wp_schedule_event( time(), 'daily', 'lalx_daily_audit' );
	}
}
add_action( 'init', 'lalx_register_cron' );

/**
 * Handle the daily audit cron event.
 *
 * @return void
 */
function lalx_handle_cron() {
	lalx_store_audit_data();
}
add_action( 'lalx_daily_audit', 'lalx_handle_cron' );

/**
 * Shortcode handler for [link_audit].
 *
 * @return string
 */
function lalx_shortcode_audit() {
	$data = get_option( 'lalx_audit_log', array() );
	if ( empty( $data ) ) {
		return esc_html__( 'No audit data available.', 'link-audit-log' );
	}

	// Set $latest to the first entry (most recent).
	$latest = $data[0];

	// translators: %1$s is the timestamp, %2$d is the count of posts with external links.
	$translated = esc_html__( 'Latest audit at %1$s: %2$d posts with external links.', 'link-audit-log' );
	return sprintf( $translated, $latest['timestamp'], $latest['count'] );
}
add_shortcode( 'link_audit', 'lalx_shortcode_audit' );

/**
 * Activation hook.
 *
 * @return void
 */
function lalx_activate() {
	// No rewrite rules to flush.
}
register_activation_hook( __FILE__, 'lalx_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function lalx_deactivate() {
	// No rewrite rules to flush.
}
register_deactivation_hook( __FILE__, 'lalx_deactivate' );
