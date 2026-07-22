<?php
/**
 * Plugin Name:       Company Footer Settings
 * Description:       Adds a company name field to settings for footer copyright notice.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       company-footer-settings
 *
 * @package Cfst
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CFST_VERSION', '1.0.0' );
define( 'CFST_FILE', __FILE__ );
define( 'CFST_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cfst_load_textdomain() {
	load_plugin_textdomain( 'company-footer-settings', false, dirname( plugin_basename( CFST_FILE ) ) . '/languages' );
}
add_action( 'init', 'cfst_load_textdomain' );

// Load admin settings.
require_once CFST_PATH . 'includes/admin-page.php';

/**
 * Output company name in footer copyright notice.
 *
 * @return void
 */
function cfst_output_footer_copyright() {
	$company_name = get_option( 'cfst_company_name', '' );
	if ( ! empty( $company_name ) ) {
		echo '<div class="cfst-footer-copyright">' . esc_html( $company_name ) . ' &copy; ' . esc_html( gmdate( 'Y' ) ) . '</div>';
	}
}
add_action( 'wp_footer', 'cfst_output_footer_copyright' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cfst_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cfst_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cfst_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cfst_deactivate' );
