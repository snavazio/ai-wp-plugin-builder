<?php
/**
 * Plugin Name:       Vehicles
 * Description:       A Vehicle CPT with make, model, and year meta (secure meta box) and a [vehicles] shortcode listing published vehicles.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       vehicles
 *
 * @package Vclt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VCLT_VERSION', '1.0.0' );
define( 'VCLT_FILE', __FILE__ );
define( 'VCLT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function vclt_load_textdomain() {
	load_plugin_textdomain( 'vehicles', false, dirname( plugin_basename( VCLT_FILE ) ) . '/languages' );
}
add_action( 'init', 'vclt_load_textdomain' );

require_once VCLT_PATH . 'includes/post-types.php';
require_once VCLT_PATH . 'includes/meta-boxes.php';
require_once VCLT_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function vclt_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'vclt_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function vclt_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'vclt_deactivate' );
