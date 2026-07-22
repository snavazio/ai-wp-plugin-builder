<?php
/**
 * Uninstall cleanup for Orphan Media Cleaner.
 *
 * @package Omclean
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete the option storing media count.
delete_option( 'omclean_media_count' );
