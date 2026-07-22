<?php
/**
 * Plugin Name:       Book Reviews
 * Description:       A Book Review CPT with meta fields for author and a 1-5 rating (clamped), editable in a secure meta box, plus an admin Rating column showing stars.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       book-reviews
 *
 * @package Brpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BRPL_VERSION', '1.0.0' );
define( 'BRPL_FILE', __FILE__ );
define( 'BRPL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function brpl_load_textdomain() {
	load_plugin_textdomain( 'book-reviews', false, dirname( plugin_basename( BRPL_FILE ) ) . '/languages' );
}
add_action( 'init', 'brpl_load_textdomain' );

// Include feature files.
require_once BRPL_PATH . 'includes/post-type.php';
require_once BRPL_PATH . 'includes/meta-box.php';
require_once BRPL_PATH . 'includes/admin-columns.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function brpl_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'brpl_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function brpl_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'brpl_deactivate' );
