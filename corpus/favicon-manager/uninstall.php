<?php
/**
 * Uninstall cleanup for Favicon Manager.
 *
 * @package Fvman
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the favicon URL option.
delete_option( 'fvman_favicon_url' );
