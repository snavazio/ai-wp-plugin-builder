<?php
/**
 * Plugin Name:       Reading Progress
 * Description:       A settings page with an enable toggle and bar color; when enabled, render an escaped reading-progress bar on single posts.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       reading-progress
 *
 * @package Rpbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RPBAR_VERSION', '1.0.0' );
define( 'RPBAR_FILE', __FILE__ );
define( 'RPBAR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rpbar_load_textdomain() {
	load_plugin_textdomain( 'reading-progress', false, dirname( plugin_basename( RPBAR_FILE ) ) . '/languages' );
}
add_action( 'init', 'rpbar_load_textdomain' );

// Include feature files.
require_once RPBAR_PATH . 'includes/admin-page.php';

/**
 * Register the Reading Progress settings page.
 *
 * @return void
 */
function rpbar_register_admin_page() {
	add_options_page(
		__( 'Reading Progress', 'reading-progress' ),
		__( 'Reading Progress', 'reading-progress' ),
		'manage_options',
		'reading-progress-settings',
		'rpbar_admin_page_callback'
	);
}
add_action( 'admin_menu', 'rpbar_register_admin_page' );

/**
 * Enqueue frontend styles for the reading progress bar.
 *
 * @return void
 */
function rpbar_enqueue_style() {
	if ( ! is_single() ) {
		return;
	}

	$color = get_option( 'rpbar_color', '#000000' );
	$color = sanitize_hex_color( $color );
	if ( ! $color ) {
		$color = '#000000';
	}

	// Register the style to avoid duplicate enqueues.
	wp_register_style( 'rpbar-progress', false );
	wp_enqueue_style( 'rpbar-progress' );

	// Add inline style for the color.
	wp_add_inline_style(
		'rpbar-progress',
		'.rpbar-progress-bar { position: fixed; top: 0; left: 0; width: 100%; height: 5px; z-index: 9999; background-color: ' . esc_attr( $color ) . '; }'
	);
}
add_action( 'wp_enqueue_scripts', 'rpbar_enqueue_style' );

/**
 * Add the reading progress bar to single post content.
 *
 * @param string $content The post content.
 * @return string Modified content.
 */
function rpbar_add_progress_bar( $content ) {
	if ( ! is_single() ) {
		return $content;
	}

	$enabled = get_option( 'rpbar_enabled', false );
	if ( ! $enabled ) {
		return $content;
	}

	$color = get_option( 'rpbar_color', '#000000' );
	$color = sanitize_hex_color( $color );
	if ( ! $color ) {
		$color = '#000000';
	}

	// Escape the color for the inline style.
	$bar = '<div class="rpbar-progress-bar" style="background-color: ' . esc_attr( $color ) . '"></div>';
	return $bar . $content;
}
add_filter( 'the_content', 'rpbar_add_progress_bar', 10, 1 );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function rpbar_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rpbar_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rpbar_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rpbar_deactivate' );
