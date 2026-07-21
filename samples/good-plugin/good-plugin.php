<?php
/**
 * Plugin Name:       Client Notes (Good Sample)
 * Description:       A clean, standards-compliant sample: a Client Notes custom post type with a rating admin column and a shortcode. Used as the harness quality bar.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       good-plugin
 * Domain Path:       /languages
 *
 * @package Good_Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CNOTE_VERSION', '1.0.0' );
define( 'CNOTE_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-cnote-plugin.php';

/**
 * Boot the plugin on plugins_loaded.
 *
 * @return void
 */
function cnote_bootstrap() {
	$plugin = new Cnote_Plugin();
	$plugin->register();
}
add_action( 'plugins_loaded', 'cnote_bootstrap' );

register_activation_hook( __FILE__, 'cnote_activate' );
register_deactivation_hook( __FILE__, 'cnote_deactivate' );

/**
 * Activation: register the post type then flush rewrite rules once.
 *
 * @return void
 */
function cnote_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cnote-plugin.php';
	$plugin = new Cnote_Plugin();
	$plugin->register_post_type();
	flush_rewrite_rules();
}

/**
 * Deactivation: flush rewrite rules so the CPT rules are removed.
 *
 * @return void
 */
function cnote_deactivate() {
	flush_rewrite_rules();
}
