<?php
/**
 * Plugin Name:       Tutorials
 * Description:       Custom post type for tutorials with difficulty taxonomy and shortcode listing.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tutorials
 *
 * @package Tutly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TUTLY_VERSION', '1.0.0' );
define( 'TUTLY_FILE', __FILE__ );
define( 'TUTLY_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function tutly_load_textdomain() {
	load_plugin_textdomain( 'tutorials', false, dirname( plugin_basename( TUTLY_FILE ) ) . '/languages' );
}
add_action( 'init', 'tutly_load_textdomain' );

require_once TUTLY_PATH . 'includes/post-types.php';
require_once TUTLY_PATH . 'includes/taxonomies.php';
require_once TUTLY_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function tutly_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tutly_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function tutly_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'tutly_deactivate' );
