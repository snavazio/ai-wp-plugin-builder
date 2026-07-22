<?php
/**
 * Uninstall cleanup for Announcement Bar.
 *
 * @package Annbar
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the settings option.
delete_option( 'annbar_settings' );
