<?php
/**
 * Plugin Name:       Current Year
 * Description:       A [current_year] shortcode that outputs the current four-digit year (useful for footers). Escaped output. No settings.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       current-year
 *
 * @package Cyrs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CYRS_VERSION', '1.0.0' );
define( 'CYRS_FILE', __FILE__ );
define( 'CYRS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cyrs_load_textdomain() {
	load_plugin_textdomain( 'current-year', false, dirname( plugin_basename( CYRS_FILE ) ) . '/languages' );
}
add_action( 'init', 'cyrs_load_textdomain' );

/**
 * Register the current_year shortcode.
 *
 * @return void
 */
function cyrs_register_shortcodes() {
	add_shortcode( 'current_year', 'cyrs_shortcode_current_year' );
}
add_action( 'init', 'cyrs_register_shortcodes' );

/**
 * Shortcode handler for [current_year].
 *
 * @return string
 */
function cyrs_shortcode_current_year() {
	$year = gmdate( 'Y' );
	return esc_html( $year );
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cyrs_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cyrs_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cyrs_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cyrs_deactivate' );
