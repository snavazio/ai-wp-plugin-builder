<?php
/**
 * Uninstall cleanup for Draft Cleanup.
 *
 * @package Dctc
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete the option storing total trashed count.
delete_option( 'dctc_total_trashed' );
