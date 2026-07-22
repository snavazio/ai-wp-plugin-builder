<?php
/**
 * Plugin Name:       AJAX Newsletter
 * Description:       A newsletter shortcode with AJAX submission for logged-out users, storing emails in an option list with deduplication.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-newsletter
 *
 * @package Anews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANEWS_VERSION', '1.0.0' );
define( 'ANEWS_FILE', __FILE__ );
define( 'ANEWS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function anews_load_textdomain() {
	load_plugin_textdomain( 'ajax-newsletter', false, dirname( plugin_basename( ANEWS_FILE ) ) . '/languages' );
}
add_action( 'init', 'anews_load_textdomain' );

// Include necessary files.
require_once ANEWS_PATH . 'includes/ajax.php';
require_once ANEWS_PATH . 'includes/assets.php';
require_once ANEWS_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function anews_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'anews_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function anews_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'anews_deactivate' );
