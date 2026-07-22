<?php
/**
 * Plugin Name:       Events Calendar
 * Description:       Event CPT with start-date and location meta, admin Start Date column, and [events] shortcode for upcoming events. All output escaped, input sanitized, with nonce and capability checks.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       events-calendar
 *
 * @package Evcal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EVCAL_VERSION', '1.0.0' );
define( 'EVCAL_FILE', __FILE__ );
define( 'EVCAL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function evcal_load_textdomain() {
	load_plugin_textdomain( 'events-calendar', false, dirname( plugin_basename( EVCAL_FILE ) ) . '/languages' );
}
add_action( 'init', 'evcal_load_textdomain' );

require_once EVCAL_PATH . 'includes/post-types.php';
require_once EVCAL_PATH . 'includes/meta-boxes.php';
require_once EVCAL_PATH . 'includes/admin-columns.php';
require_once EVCAL_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function evcal_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'evcal_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function evcal_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'evcal_deactivate' );
