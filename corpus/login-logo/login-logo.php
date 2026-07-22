<?php
/**
 * Plugin Name:       Login Logo
 * Description:       Adds a settings page to replace the WordPress login logo with a custom image URL.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       login-logo
 *
 * @package Lgnl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LGNL_VERSION', '1.0.0' );
define( 'LGNL_FILE', __FILE__ );
define( 'LGNL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function lgnl_load_textdomain() {
	load_plugin_textdomain( 'login-logo', false, dirname( plugin_basename( LGNL_FILE ) ) . '/languages' );
}
add_action( 'init', 'lgnl_load_textdomain' );

// Include feature files.
require_once LGNL_PATH . 'includes/admin-page.php';

/**
 * Output custom login logo CSS.
 *
 * @return void
 */
function lgnl_output_login_logo_css() {
	$logo_url = get_option( 'lgnl_logo_url', '' );
	if ( empty( $logo_url ) ) {
		return;
	}

	// Escape URL at point of output.
	?>
	<style type="text/css">
		.login h1 a {
			background-image: url('<?php echo esc_url( $logo_url ); ?>') !important;
			background-size: contain !important;
			width: 320px !important;
			height: 80px !important;
			margin: 0 auto !important;
		}
	</style>
	<?php
}
add_action( 'login_head', 'lgnl_output_login_logo_css' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function lgnl_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'lgnl_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function lgnl_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'lgnl_deactivate' );
