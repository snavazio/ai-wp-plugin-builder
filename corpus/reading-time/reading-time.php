<?php
/**
 * Plugin Name:       Reading Time
 * Description:       Adds a 'X min read' estimate to single post content when enabled via a settings toggle.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       reading-time
 *
 * @package Rtmin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RTMIN_VERSION', '1.0.0' );
define( 'RTMIN_FILE', __FILE__ );
define( 'RTMIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rtmin_load_textdomain() {
	load_plugin_textdomain( 'reading-time', false, dirname( plugin_basename( RTMIN_FILE ) ) . '/languages' );
}
add_action( 'init', 'rtmin_load_textdomain' );

// Include feature files.
require_once RTMIN_PATH . 'includes/admin-page.php';

/**
 * Register the Reading Time settings page.
 *
 * @return void
 */
function rtmin_register_admin_page() {
	add_options_page(
		__( 'Reading Time Settings', 'reading-time' ),
		__( 'Reading Time', 'reading-time' ),
		'manage_options',
		'reading-time-settings',
		'rtmin_admin_page_callback'
	);
}
add_action( 'admin_menu', 'rtmin_register_admin_page' );

/**
 * Add reading time estimate to single post content.
 *
 * @param string $content The post content.
 * @return string Modified content.
 */
function rtmin_add_reading_time( $content ) {
	if ( ! is_single() ) {
		return $content;
	}

	$enabled = get_option( 'rtmin_enabled', false );
	if ( ! $enabled ) {
		return $content;
	}

	// Calculate reading time (simple: 200 words per minute).
	$word_count = str_word_count( strip_tags( $content ) );
	$minutes    = ceil( $word_count / 200 );

	$estimate = sprintf(
		// translators: %d is the number of minutes.
		__( '%d min read', 'reading-time' ),
		$minutes
	);

	// Escape output at point of echo.
	// Prepend to content with proper escaping.
	return '<div class="rtmin-reading-time">' . esc_html( $estimate ) . '</div>' . $content;
}
add_filter( 'the_content', 'rtmin_add_reading_time', 5, 1 );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function rtmin_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rtmin_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rtmin_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rtmin_deactivate' );
