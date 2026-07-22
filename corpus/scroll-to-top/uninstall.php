<?php
/**
 * Uninstall cleanup for Scroll To Top.
 *
 * @package Sttop
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the settings option.
delete_option( 'sttop_settings' );
