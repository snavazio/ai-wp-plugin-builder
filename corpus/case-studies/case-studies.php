<?php
/**
 * Plugin Name:       Case Studies
 * Description:       A custom post type for case studies with client and outcome meta, admin column, and shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       case-studies
 *
 * @package Cstuds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CSTUDS_VERSION', '1.0.0' );
define( 'CSTUDS_FILE', __FILE__ );
define( 'CSTUDS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cstuds_load_textdomain() {
	load_plugin_textdomain( 'case-studies', false, dirname( plugin_basename( CSTUDS_FILE ) ) . '/languages' );
}
add_action( 'init', 'cstuds_load_textdomain' );

require_once CSTUDS_PATH . 'includes/post-types.php';
require_once CSTUDS_PATH . 'includes/meta-boxes.php';
require_once CSTUDS_PATH . 'includes/admin-columns.php';
require_once CSTUDS_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cstuds_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cstuds_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cstuds_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cstuds_deactivate' );
