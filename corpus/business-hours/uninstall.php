<?php
/**
 * Uninstall cleanup for Business Hours.
 *
 * @package Bhrs
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the business hours option.
delete_option( 'bhrs_business_hours' );
