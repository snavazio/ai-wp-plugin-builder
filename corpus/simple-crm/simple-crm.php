<?php
/**
 * Plugin Name:       Simple CRM
 * Description:       A Contact CPT with meta fields for email and phone (secure meta box), a hierarchical Status taxonomy (e.g. Lead/Customer), an admin Status column, and a [contacts status=""] shortcode listing contacts by status, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check. Remove data and terms on uninstall.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-crm
 *
 * @package Scrm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SCRM_VERSION', '1.0.0' );
define( 'SCRM_FILE', __FILE__ );
define( 'SCRM_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function scrm_load_textdomain() {
	load_plugin_textdomain( 'simple-crm', false, dirname( plugin_basename( SCRM_FILE ) ) . '/languages' );
}
add_action( 'init', 'scrm_load_textdomain' );

require_once SCRM_PATH . 'includes/post-types.php';
require_once SCRM_PATH . 'includes/taxonomies.php';
require_once SCRM_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function scrm_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'scrm_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function scrm_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'scrm_deactivate' );
