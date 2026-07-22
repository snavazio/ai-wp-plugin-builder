<?php
/**
 * Plugin Name:       Rentals
 * Description:       A Rental CPT with price and bedrooms meta (secure meta box), an admin Price column, and a [rentals] shortcode listing published rentals, escaped.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rentals
 *
 * @package Rent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RENT_VERSION', '1.0.0' );
define( 'RENT_FILE', __FILE__ );
define( 'RENT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rent_load_textdomain() {
	load_plugin_textdomain( 'rentals', false, dirname( plugin_basename( RENT_FILE ) ) . '/languages' );
}
add_action( 'init', 'rent_load_textdomain' );

require_once RENT_PATH . 'includes/post-types.php';
require_once RENT_PATH . 'includes/meta-boxes.php';
require_once RENT_PATH . 'includes/admin-columns.php';
require_once RENT_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function rent_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rent_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rent_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rent_deactivate' );
