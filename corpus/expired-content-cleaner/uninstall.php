<?php
/**
 * Uninstall cleanup for Expired Content Cleaner.
 *
 * @package Expcl
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete the option storing total expired posts count.
delete_option( 'expired_content_cleaner_count' );
