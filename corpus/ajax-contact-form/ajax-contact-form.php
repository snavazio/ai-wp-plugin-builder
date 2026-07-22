<?php
/**
 * Plugin Name:       AJAX Contact Form
 * Description:       A contact form shortcode with AJAX submission, storing submissions as private CPT
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-contact-form
 *
 * @package Acff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ACFF_VERSION', '1.0.0' );
define( 'ACFF_FILE', __FILE__ );
define( 'ACFF_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function acff_load_textdomain() {
	load_plugin_textdomain( 'ajax-contact-form', false, dirname( plugin_basename( ACFF_FILE ) ) . '/languages' );
}
add_action( 'init', 'acff_load_textdomain' );

// Include necessary files.
require_once ACFF_PATH . 'includes/post-types.php';
require_once ACFF_PATH . 'includes/shortcode.php';
require_once ACFF_PATH . 'includes/ajax.php';
require_once ACFF_PATH . 'includes/assets.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function acff_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'acff_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function acff_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'acff_deactivate' );
