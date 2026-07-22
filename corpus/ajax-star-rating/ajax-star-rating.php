<?php
/**
 * Plugin Name:       AJAX Star Rating
 * Description:       A [star_rating] shortcode showing 1-5 stars for the current post. Submitting a rating via AJAX (logged-out allowed) updates a running average in post meta.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-star-rating
 *
 * @package Arsr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARSR_VERSION', '1.0.0' );
define( 'ARSR_FILE', __FILE__ );
define( 'ARSR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function arsr_load_textdomain() {
	load_plugin_textdomain( 'ajax-star-rating', false, dirname( plugin_basename( ARSR_FILE ) ) . '/languages' );
}
add_action( 'init', 'arsr_load_textdomain' );

// Include necessary files.
require_once ARSR_PATH . 'includes/ajax.php';
require_once ARSR_PATH . 'includes/assets.php';
require_once ARSR_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function arsr_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'arsr_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function arsr_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'arsr_deactivate' );
