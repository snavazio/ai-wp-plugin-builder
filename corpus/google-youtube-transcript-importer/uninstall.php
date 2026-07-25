<?php
/**
 * Uninstall cleanup for Google YouTube Transcript Importer.
 *
 * @package Gyti
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove stored Google OAuth token.
delete_option( 'gyti_google_token' );
