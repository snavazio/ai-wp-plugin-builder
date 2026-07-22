<?php
/**
 * Plugin Name:       Vote Poll
 * Description:       A shortcode for yes/no polls with AJAX voting, storing counts in options.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       vote-poll
 *
 * @package Vtpol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VTPOL_VERSION', '1.0.0' );
define( 'VTPOL_FILE', __FILE__ );
define( 'VTPOL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function vtpol_load_textdomain() {
	load_plugin_textdomain( 'vote-poll', false, dirname( plugin_basename( VTPOL_FILE ) ) . '/languages' );
}
add_action( 'init', 'vtpol_load_textdomain' );

// Include necessary files.
require_once VTPOL_PATH . 'includes/ajax.php';
require_once VTPOL_PATH . 'includes/assets.php';
require_once VTPOL_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function vtpol_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'vtpol_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function vtpol_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'vtpol_deactivate' );
