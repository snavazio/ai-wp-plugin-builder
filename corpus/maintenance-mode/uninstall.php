<?php
/**
 * Uninstall cleanup for Maintenance Mode.
 *
 * @package Mmtm
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the settings option.
delete_option( 'mmtm_settings' );
