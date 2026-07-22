<?php
/**
 * Plugin Name:       Movie Reviews
 * Description:       A Movie CPT with a rating meta (secure meta box) and a Genre taxonomy, plus a [movies genre=""] shortcode listing published movies by genre.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       movie-reviews
 *
 * @package Mrev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MREV_VERSION', '1.0.0' );
define( 'MREV_FILE', __FILE__ );
define( 'MREV_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function mrev_load_textdomain() {
	load_plugin_textdomain( 'movie-reviews', false, dirname( plugin_basename( MREV_FILE ) ) . '/languages' );
}
add_action( 'init', 'mrev_load_textdomain' );

require_once MREV_PATH . 'includes/post-types.php';
require_once MREV_PATH . 'includes/taxonomies.php';
require_once MREV_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function mrev_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mrev_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function mrev_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'mrev_deactivate' );
