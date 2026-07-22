<?php
/**
 * Uninstall cleanup for Contact Info.
 *
 * @package Cinfo
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the contact info option.
delete_option( 'cinfo_contact_info' );
