<?php
/**
 * Plugin Name:       AJAX Load Comments
 * Description:       Fetches next page of approved comments via AJAX using [load_comments] shortcode, supporting logged-out users.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-load-comments
 *
 * @package Alcp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALCP_VERSION', '1.0.0' );
define( 'ALCP_FILE', __FILE__ );
define( 'ALCP_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function alcp_load_textdomain() {
	load_plugin_textdomain( 'ajax-load-comments', false, dirname( plugin_basename( ALCP_FILE ) ) . '/languages' );
}
add_action( 'init', 'alcp_load_textdomain' );

// Include necessary files.
require_once ALCP_PATH . 'includes/ajax.php';
require_once ALCP_PATH . 'includes/assets.php';
require_once ALCP_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function alcp_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'alcp_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function alcp_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'alcp_deactivate' );
