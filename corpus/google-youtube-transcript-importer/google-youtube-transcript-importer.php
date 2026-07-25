<?php
/**
 * Plugin Name:       Google YouTube Transcript Importer
 * Description:       Connects to Google account to import YouTube videos and retrieve their transcripts.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       google-youtube-transcript-importer
 *
 * @package Gyti
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GYTI_VERSION', '1.0.0' );
define( 'GYTI_FILE', __FILE__ );
define( 'GYTI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function gyti_load_textdomain() {
	load_plugin_textdomain( 'google-youtube-transcript-importer', false, dirname( plugin_basename( GYTI_FILE ) ) . '/languages' );
}
add_action( 'init', 'gyti_load_textdomain' );

// Include necessary files.
require_once GYTI_PATH . 'includes/storage.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function gyti_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gyti_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function gyti_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'gyti_deactivate' );
