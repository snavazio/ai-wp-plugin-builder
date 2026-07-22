<?php
/**
 * Uninstall cleanup for Reading Progress.
 *
 * @package Rpbar
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove plugin options.
delete_option( 'rpbar_enabled' );
delete_option( 'rpbar_color' );
