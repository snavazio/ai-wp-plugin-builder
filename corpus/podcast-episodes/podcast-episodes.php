<?php
/**
 * Plugin Name:       Podcast Episodes
 * Description:       Manage podcast episodes with audio URL, duration meta, and season taxonomy.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       podcast-episodes
 *
 * @package Podc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PODC_VERSION', '1.0.0' );
define( 'PODC_FILE', __FILE__ );
define( 'PODC_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function podc_load_textdomain() {
	load_plugin_textdomain( 'podcast-episodes', false, dirname( plugin_basename( PODC_FILE ) ) . '/languages' );
}
add_action( 'init', 'podc_load_textdomain' );

require_once PODC_PATH . 'includes/post-types.php';
require_once PODC_PATH . 'includes/taxonomies.php';
require_once PODC_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function podc_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'podc_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function podc_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'podc_deactivate' );
