<?php
/**
 * Plugin Name:       Countdown Block
 * Description:       A dynamic server-rendered Gutenberg block that renders a countdown to a target date attribute (sanitized), showing days remaining, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       countdown-block
 *
 * @package Cdtblk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CDTBLK_VERSION', '1.0.0' );
define( 'CDTBLK_FILE', __FILE__ );
define( 'CDTBLK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cdtblk_load_textdomain() {
	load_plugin_textdomain( 'countdown-block', false, dirname( plugin_basename( CDTBLK_FILE ) ) . '/languages' );
}
add_action( 'init', 'cdtblk_load_textdomain' );

/**
 * Register the Countdown Block.
 *
 * @return void
 */
function cdtblk_register_countdown_block() {
	register_block_type(
		'countdown-block/countdown',
		array(
			'render_callback' => 'cdtblk_render_countdown_block',
		)
	);
}
add_action( 'init', 'cdtblk_register_countdown_block' );

/**
 * Render the Countdown Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function cdtblk_render_countdown_block( $attributes ) {
	// Sanitize target date attribute.
	$target_date = isset( $attributes['targetDate'] ) ? sanitize_text_field( $attributes['targetDate'] ) : '';

	// Validate date format (YYYY-MM-DD).
	$date = DateTime::createFromFormat( 'Y-m-d', $target_date );
	if ( ! $date || $date->format( 'Y-m-d' ) !== $target_date ) {
		// Use current date as fallback for invalid input (UTC).
		$target_date = gmdate( 'Y-m-d' );
	}

	// Calculate days remaining.
	$now      = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
	$target   = new DateTime( $target_date, new DateTimeZone( 'UTC' ) );
	$interval = $now->diff( $target );
	$days     = $interval->days;

	// Format message based on date status.
	if ( $now > $target ) {
		$message = esc_html__( 'Countdown expired', 'countdown-block' );
	} else {
		// translators: %d is the number of days remaining.
		$message = sprintf( esc_html__( '%d days remaining', 'countdown-block' ), (string) $days );
	}

	// Escape all output.
	$output  = '<div class="cdtblk-countdown">';
	$output .= '<span class="cdtblk-days">' . esc_html( (string) $days ) . '</span>';
	$output .= '<span class="cdtblk-message">' . $message . '</span>';
	$output .= '</div>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cdtblk_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cdtblk_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cdtblk_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cdtblk_deactivate' );
