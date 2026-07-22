<?php
/**
 * Plugin Name:       Reaction Buttons
 * Description:       A shortcode displaying emoji reaction buttons for posts, with AJAX click tracking stored in post meta.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       reaction-buttons
 *
 * @package Reac
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'REAC_VERSION', '1.0.0' );
define( 'REAC_FILE', __FILE__ );
define( 'REAC_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function reac_load_textdomain() {
	load_plugin_textdomain( 'reaction-buttons', false, dirname( plugin_basename( REAC_FILE ) ) . '/languages' );
}
add_action( 'init', 'reac_load_textdomain' );

// Include necessary files.
require_once REAC_PATH . 'includes/ajax.php';
require_once REAC_PATH . 'includes/assets.php';
require_once REAC_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function reac_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'reac_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function reac_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'reac_deactivate' );
