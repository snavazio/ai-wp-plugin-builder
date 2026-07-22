<?php
/**
 * Uninstall cleanup for AJAX Newsletter.
 *
 * @package Anews
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete the option that stores the email list.
delete_option( 'anews_emails' );
