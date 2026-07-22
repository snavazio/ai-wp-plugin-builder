<?php
/**
 * Uninstall cleanup for Back To Top Button.
 *
 * @package Bttb
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the settings option.
delete_option( 'bttb_settings' );
