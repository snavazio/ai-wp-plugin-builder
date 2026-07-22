<?php
/**
 * Plugin Name:       Recipes
 * Description:       A Recipe Custom Post Type with meta fields for prep time and servings, secure meta box, and recipe_card shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       recipes
 *
 * @package Rcpr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RCPR_VERSION', '1.0.0' );
define( 'RCPR_FILE', __FILE__ );
define( 'RCPR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rcpr_load_textdomain() {
	load_plugin_textdomain( 'recipes', false, dirname( plugin_basename( RCPR_FILE ) ) . '/languages' );
}
add_action( 'init', 'rcpr_load_textdomain' );

require_once RCPR_PATH . 'includes/post-types.php';
require_once RCPR_PATH . 'includes/meta-boxes.php';
require_once RCPR_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function rcpr_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rcpr_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rcpr_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rcpr_deactivate' );
