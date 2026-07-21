<?php
/**
 * Plugin Name:       Client Notes (Bad Sample)
 * Description:       The SAME plugin as the good sample but with deliberately planted vulnerabilities. Used to prove the harness REJECTS insecure code on the right gate.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bad-plugin
 * Domain Path:       /languages
 *
 * @package Bad_Plugin
 */

// PLANTED VULN #1: no `if ( ! defined( 'ABSPATH' ) ) { exit; }` direct-access guard.

define( 'BADP_VERSION', '1.0.0' );

require_once plugin_dir_path( __FILE__ ) . 'includes/handlers.php';

add_action( 'init', 'badp_register_post_type' );
add_shortcode( 'client_notes', 'badp_render_shortcode' );

/**
 * Register the Client Notes CPT.
 *
 * @return void
 */
function badp_register_post_type() {
	register_post_type(
		'badp_note',
		array(
			'label'        => 'Client Notes',
			'public'       => false,
			'show_ui'      => true,
			'show_in_rest' => true,
		)
	);
}

/**
 * Shortcode that greets the visitor by a URL parameter.
 *
 * @return string
 */
function badp_render_shortcode() {
	// PLANTED VULN #2: unsanitized, unescaped output of a superglobal (reflected XSS).
	$name = $_GET['name'];
	echo 'Hello ' . $name;

	return '';
}
