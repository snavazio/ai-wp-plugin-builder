<?php
/**
 * Plugin Name:       Quotes Collection
 * Description:       A plugin for managing and displaying quotes with a custom post type and shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       quotes-collection
 *
 * @package Qcl1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'QCL1_VERSION', '1.0.0' );
define( 'QCL1_FILE', __FILE__ );
define( 'QCL1_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function qcl1_load_textdomain() {
	load_plugin_textdomain( 'quotes-collection', false, dirname( plugin_basename( QCL1_FILE ) ) . '/languages' );
}
add_action( 'init', 'qcl1_load_textdomain' );

require_once QCL1_PATH . 'includes/post-types.php';
require_once QCL1_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function qcl1_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'qcl1_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function qcl1_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'qcl1_deactivate' );
