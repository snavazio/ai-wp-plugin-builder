<?php
/**
 * Plugin Name:       Portfolio
 * Description:       A plugin for showcasing agency projects with custom post type, taxonomy, shortcode, and AJAX load-more functionality.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       portfolio
 *
 * @package Prtf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PRTF_VERSION', '1.0.0' );
define( 'PRTF_FILE', __FILE__ );
define( 'PRTF_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function prtf_load_textdomain() {
	load_plugin_textdomain( 'portfolio', false, dirname( plugin_basename( PRTF_FILE ) ) . '/languages' );
}
add_action( 'init', 'prtf_load_textdomain' );

// Load plugin components.
require_once PRTF_PATH . 'includes/post-types.php';
require_once PRTF_PATH . 'includes/taxonomies.php';
require_once PRTF_PATH . 'includes/shortcode.php';
require_once PRTF_PATH . 'includes/ajax.php';
require_once PRTF_PATH . 'includes/assets.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function prtf_activate() {
	prtf_register_post_types();
	prtf_register_taxonomies();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'prtf_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function prtf_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'prtf_deactivate' );
