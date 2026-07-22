<?php
/**
 * Plugin Name:       Workshop CPT Manager
 * Description:       A plugin for managing workshops as a custom post type with date and seats meta.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       workshop-cpt
 *
 * @package Wcpm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WCPM_VERSION', '1.0.0' );
define( 'WCPM_FILE', __FILE__ );
define( 'WCPM_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function wcpm_load_textdomain() {
	load_plugin_textdomain( 'workshop-cpt', false, dirname( plugin_basename( WCPM_FILE ) ) . '/languages' );
}
add_action( 'init', 'wcpm_load_textdomain' );

require_once WCPM_PATH . 'includes/post-types.php';
require_once WCPM_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function wcpm_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wcpm_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function wcpm_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'wcpm_deactivate' );
