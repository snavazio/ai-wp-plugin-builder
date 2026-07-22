<?php
/**
 * Plugin Name:       Business Directory
 * Description:       A Business CPT with hierarchical Category and Region taxonomies, plus a [directory] shortcode for listing businesses.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       business-directory
 *
 * @package Bdir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BDIR_VERSION', '1.0.0' );
define( 'BDIR_FILE', __FILE__ );
define( 'BDIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function bdir_load_textdomain() {
	load_plugin_textdomain( 'business-directory', false, dirname( plugin_basename( BDIR_FILE ) ) . '/languages' );
}
add_action( 'init', 'bdir_load_textdomain' );

require_once BDIR_PATH . 'includes/post-types.php';
require_once BDIR_PATH . 'includes/taxonomies.php';
require_once BDIR_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function bdir_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bdir_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function bdir_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'bdir_deactivate' );
