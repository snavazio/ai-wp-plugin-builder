<?php
/**
 * Plugin Name:       Beer List
 * Description:       A custom post type for beers with ABV meta and style taxonomy, featuring a shortcode for filtering.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       beer-list
 *
 * @package Beers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BEERS_VERSION', '1.0.0' );
define( 'BEERS_FILE', __FILE__ );
define( 'BEERS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function beers_load_textdomain() {
	load_plugin_textdomain( 'beer-list', false, dirname( plugin_basename( BEERS_FILE ) ) . '/languages' );
}
add_action( 'init', 'beers_load_textdomain' );

require_once BEERS_PATH . 'includes/post-types.php';
require_once BEERS_PATH . 'includes/taxonomies.php';
require_once BEERS_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function beers_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'beers_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function beers_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'beers_deactivate' );
