<?php
/**
 * Plugin Name:       Quick Search
 * Description:       Adds a [quick_search] shortcode that provides title suggestions via AJAX for published posts.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       quick-search
 *
 * @package Qsrch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'QSRCH_VERSION', '1.0.0' );
define( 'QSRCH_FILE', __FILE__ );
define( 'QSRCH_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function qsrch_load_textdomain() {
	load_plugin_textdomain( 'quick-search', false, dirname( plugin_basename( QSRCH_FILE ) ) . '/languages' );
}
add_action( 'init', 'qsrch_load_textdomain' );

// Include necessary files.
require_once QSRCH_PATH . 'includes/ajax.php';
require_once QSRCH_PATH . 'includes/assets.php';
require_once QSRCH_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function qsrch_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'qsrch_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function qsrch_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'qsrch_deactivate' );
