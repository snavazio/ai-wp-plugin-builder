<?php
/**
 * Uninstall cleanup for Custom Footer Text.
 *
 * @package Cftx
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the footer text option.
delete_option( 'cftx_footer_text' );
