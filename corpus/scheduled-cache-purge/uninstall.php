<?php
/**
 * Uninstall cleanup for Scheduled Cache Purge.
 *
 * @package Scpc
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete the option storing last purge time.
delete_option( 'scpc_last_purge' );
