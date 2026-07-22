<?php
/**
 * Plugin Name:       Products Catalog
 * Description:       A catalog of products with hierarchical categories and a shortcode for listing.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       products-catalog
 *
 * @package Prod
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PROD_VERSION', '1.0.0' );
define( 'PROD_FILE', __FILE__ );
define( 'PROD_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function prod_load_textdomain() {
	load_plugin_textdomain( 'products-catalog', false, dirname( plugin_basename( PROD_FILE ) ) . '/languages' );
}
add_action( 'init', 'prod_load_textdomain' );

require_once PROD_PATH . 'includes/post-types.php';
require_once PROD_PATH . 'includes/taxonomies.php';
require_once PROD_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function prod_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'prod_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function prod_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'prod_deactivate' );
