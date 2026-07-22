<?php
/**
 * Uninstall cleanup for Login Logo.
 *
 * @package Lgnl
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the logo URL option.
delete_option( 'lgnl_logo_url' );
