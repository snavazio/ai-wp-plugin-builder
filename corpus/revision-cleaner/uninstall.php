<?php
/**
 * Uninstall cleanup for Revision Cleaner.
 *
 * @package Revcln
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete the option storing total deleted revisions count.
delete_option( 'revcln_deleted_count' );
