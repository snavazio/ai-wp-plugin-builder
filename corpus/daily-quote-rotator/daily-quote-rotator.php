<?php
/**
 * Plugin Name:       Daily Quote Rotator
 * Description:       Schedule a daily WP-Cron event that picks a random quote from a stored list and sets it as the 'quote of the day' option. A [quote_of_day] shortcode renders it, escaped. Register/clear on activation/deactivation. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       daily-quote-rotator
 *
 * @package Daily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DAILY_VERSION', '1.0.0' );
define( 'DAILY_FILE', __FILE__ );
define( 'DAILY_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function daily_load_textdomain() {
	load_plugin_textdomain( 'daily-quote-rotator', false, dirname( plugin_basename( DAILY_FILE ) ) . '/languages' );
}
add_action( 'init', 'daily_load_textdomain' );

/**
 * Register the daily cron event.
 *
 * @return void
 */
function daily_register_cron() {
	if ( ! wp_next_scheduled( 'daily_quote_rotator' ) ) {
		wp_schedule_event( time(), 'daily', 'daily_quote_rotator' );
	}
}
add_action( 'init', 'daily_register_cron' );

/**
 * Handle the daily quote rotator cron event.
 *
 * @return void
 */
function daily_rotator_cron() {
	$quotes = array(
		'The only way to do great work is to love what you do. - Steve Jobs',
		'In the end, we only get one life to live. - Unknown',
		'The best way to predict the future is to invent it. - Alan Kay',
		"Life is what happens when you're busy making other plans. - John Lennon",
		'The greatest glory in living lies not in never falling, but in rising every time we fall. - Nelson Mandela',
		'The only thing we have to fear is fear itself. - Franklin D. Roosevelt',
		'The only true wisdom is in knowing you know nothing. - Socrates',
		'It does not matter how slowly you go as long as you do not stop. - Confucius',
		'The journey of a thousand miles begins with a single step. - Lao Tzu',
		'To be yourself in a world that is constantly trying to make you something else is the greatest accomplishment. - Ralph Waldo Emerson',
	);

	$random_quote = $quotes[ array_rand( $quotes ) ];
	update_option( 'daily_quote_of_day', $random_quote );
}
add_action( 'daily_quote_rotator', 'daily_rotator_cron' );

/**
 * Shortcode handler for [quote_of_day].
 *
 * @return string
 */
function daily_shortcode_quote_of_day() {
	$quote = get_option( 'daily_quote_of_day', '' );
	if ( empty( $quote ) ) {
		return esc_html__( 'No quote available.', 'daily-quote-rotator' );
	}
	return esc_html( $quote );
}
add_shortcode( 'quote_of_day', 'daily_shortcode_quote_of_day' );

/**
 * Activation hook.
 *
 * @return void
 */
function daily_activate() {
	// No rewrite rules to flush.
}
register_activation_hook( __FILE__, 'daily_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function daily_deactivate() {
	// No rewrite rules to flush.
}
register_deactivation_hook( __FILE__, 'daily_deactivate' );
