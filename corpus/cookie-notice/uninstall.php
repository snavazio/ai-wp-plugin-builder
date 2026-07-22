<?php
/**
 * Uninstall cleanup for Cookie Notice.
 *
 * @package Cns1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the settings option.
delete_option( 'cns1_settings' );
