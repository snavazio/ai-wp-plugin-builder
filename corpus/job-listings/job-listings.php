<?php
/**
 * Plugin Name:       Job Listings
 * Description:       A Job CPT with meta for location, employment type, and salary range, admin columns, and [jobs] shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       job-listings
 *
 * @package Jobl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JOBL_VERSION', '1.0.0' );
define( 'JOBL_FILE', __FILE__ );
define( 'JOBL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function jobl_load_textdomain() {
	load_plugin_textdomain( 'job-listings', false, dirname( plugin_basename( JOBL_FILE ) ) . '/languages' );
}
add_action( 'init', 'jobl_load_textdomain' );

require_once JOBL_PATH . 'includes/post-types.php';
require_once JOBL_PATH . 'includes/meta-boxes.php';
require_once JOBL_PATH . 'includes/admin-columns.php';
require_once JOBL_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function jobl_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'jobl_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function jobl_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'jobl_deactivate' );
