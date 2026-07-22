<?php
/**
 * Uninstall cleanup for Reading Time.
 *
 * @package Rtmin
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove plugin option.
delete_option( 'rtmin_enabled' );
