<?php
/**
 * Plugin Name:       Business Directory Pro
 * Description:       A Listing CPT with phone and website meta (secure meta box), a hierarchical Category taxonomy and a Region taxonomy, an admin Category column, a [listings category=""] shortcode, and a public read-only REST endpoint (GET) returning published listings.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       business-directory-pro
 *
 * @package Bdp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BDP_VERSION', '1.0.0' );
define( 'BDP_FILE', __FILE__ );
define( 'BDP_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function bdp_load_textdomain() {
	load_plugin_textdomain( 'business-directory-pro', false, dirname( plugin_basename( BDP_FILE ) ) . '/languages' );
}
add_action( 'init', 'bdp_load_textdomain' );

require_once BDP_PATH . 'includes/post-types.php';
require_once BDP_PATH . 'includes/taxonomies.php';
require_once BDP_PATH . 'includes/shortcodes.php';
require_once BDP_PATH . 'includes/rest.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function bdp_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bdp_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function bdp_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'bdp_deactivate' );
