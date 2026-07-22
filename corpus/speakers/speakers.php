<?php
/**
 * Plugin Name:       Speakers
 * Description:       A Speaker CPT with title and twitter meta (secure meta box), an admin column, and a [speakers] shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       speakers
 *
 * @package Spkrs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPKRS_VERSION', '1.0.0' );
define( 'SPKRS_FILE', __FILE__ );
define( 'SPKRS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function spkrs_load_textdomain() {
	load_plugin_textdomain( 'speakers', false, dirname( plugin_basename( SPKRS_FILE ) ) . '/languages' );
}
add_action( 'init', 'spkrs_load_textdomain' );

require_once SPKRS_PATH . 'includes/post-types.php';
require_once SPKRS_PATH . 'includes/meta-boxes.php';
require_once SPKRS_PATH . 'includes/admin-columns.php';
require_once SPKRS_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function spkrs_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'spkrs_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function spkrs_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'spkrs_deactivate' );
