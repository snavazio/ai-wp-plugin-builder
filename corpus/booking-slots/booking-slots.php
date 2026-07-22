<?php
/**
 * Plugin Name:       Booking Slots
 * Description:       A Slot CPT with a datetime meta (secure meta box), a [slots] shortcode listing available published slots each with a 'book' button, and an AJAX handler (logged-out allowed) that marks a slot booked in post meta after verifying a nonce and sanitizing input, returning an escaped result. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       booking-slots
 *
 * @package Bkslot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BKSLOT_VERSION', '1.0.0' );
define( 'BKSLOT_FILE', __FILE__ );
define( 'BKSLOT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function bkslot_load_textdomain() {
	load_plugin_textdomain( 'booking-slots', false, dirname( plugin_basename( BKSLOT_FILE ) ) . '/languages' );
}
add_action( 'init', 'bkslot_load_textdomain' );

// Include necessary files.
require_once BKSLOT_PATH . 'includes/post-types.php';
require_once BKSLOT_PATH . 'includes/meta-boxes.php';
require_once BKSLOT_PATH . 'includes/shortcodes.php';
require_once BKSLOT_PATH . 'includes/ajax.php';
require_once BKSLOT_PATH . 'includes/assets.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function bkslot_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bkslot_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function bkslot_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'bkslot_deactivate' );
