<?php
/**
 * Uninstall cleanup for Business Hours.
 *
 * @package Bhrs
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all business hours options.
$days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );

foreach ( $days as $day ) {
	delete_option( 'bhrs_' . $day . '_open' );
	delete_option( 'bhrs_' . $day . '_close' );
	delete_option( 'bhrs_' . $day . '_closed' );
}
