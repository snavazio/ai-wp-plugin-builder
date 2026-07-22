<?php
/**
 * Uninstall cleanup for Custom Admin Footer.
 *
 * @package Caf1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the footer text option.
delete_option( 'caf1_footer_text' );
