<?php
/**
 * Uninstall cleanup for Post Archiver.
 *
 * @package Parch
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete the option storing total archived count.
delete_option( 'parch_total_archived' );
