<?php
/**
 * Uninstall cleanup for Custom 404 Message.
 *
 * @package C404m
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the custom 404 message option.
delete_option( 'c404m_message' );
