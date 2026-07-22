<?php
/**
 * Uninstall cleanup for Disable Comments.
 *
 * @package Dcmn
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the plugin option.
delete_option( 'dcmn_comments_enabled' );
