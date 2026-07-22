<?php
/**
 * Plugin Name:       Plant Guide
 * Description:       A plugin for managing plant species with care-level metadata, hierarchical family taxonomy, and a shortcode for displaying plant families.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       plant-guide
 *
 * @package Pgui
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PGUI_VERSION', '1.0.0' );
define( 'PGUI_FILE', __FILE__ );
define( 'PGUI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function pgui_load_textdomain() {
	load_plugin_textdomain( 'plant-guide', false, dirname( plugin_basename( PGUI_FILE ) ) . '/languages' );
}
add_action( 'init', 'pgui_load_textdomain' );

require_once PGUI_PATH . 'includes/post-types.php';
require_once PGUI_PATH . 'includes/taxonomies.php';
require_once PGUI_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function pgui_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pgui_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function pgui_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pgui_deactivate' );
