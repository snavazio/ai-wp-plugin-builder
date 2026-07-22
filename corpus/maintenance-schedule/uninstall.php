<?php
/**
 * Uninstall cleanup for Maintenance Schedule.
 *
 * @package Mstg
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the settings option.
delete_option( 'mstg_settings' );
