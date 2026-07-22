<?php
/**
 * Plugin Name:       Cookbook
 * Description:       A Recipe CPT with Course and Cuisine taxonomies, and a [recipes] shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cookbook
 *
 * @package Cook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'COOK_VERSION', '1.0.0' );
define( 'COOK_FILE', __FILE__ );
define( 'COOK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cook_load_textdomain() {
	load_plugin_textdomain( 'cookbook', false, dirname( plugin_basename( COOK_FILE ) ) . '/languages' );
}
add_action( 'init', 'cook_load_textdomain' );

require_once COOK_PATH . 'includes/post-types.php';
require_once COOK_PATH . 'includes/taxonomies.php';
require_once COOK_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cook_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cook_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cook_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cook_deactivate' );
