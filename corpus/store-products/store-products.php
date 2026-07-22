<?php
/**
 * Plugin Name:       Store Products
 * Description:       A Product CPT with price and sku meta (secure meta box), an admin Price column, and a [store] shortcode listing published products.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       store-products
 *
 * @package Strp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STRP_VERSION', '1.0.0' );
define( 'STRP_FILE', __FILE__ );
define( 'STRP_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function strp_load_textdomain() {
	load_plugin_textdomain( 'store-products', false, dirname( plugin_basename( STRP_FILE ) ) . '/languages' );
}
add_action( 'init', 'strp_load_textdomain' );

require_once STRP_PATH . 'includes/post-types.php';
require_once STRP_PATH . 'includes/meta-boxes.php';
require_once STRP_PATH . 'includes/admin-columns.php';
require_once STRP_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function strp_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'strp_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function strp_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'strp_deactivate' );
