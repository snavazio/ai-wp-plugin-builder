<?php
/**
 * Uninstall cleanup for External Link Icons.
 *
 * @package Elink
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the plugin option.
delete_option( 'elink_enabled' );
