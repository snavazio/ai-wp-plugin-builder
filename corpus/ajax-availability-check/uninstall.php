<?php
/**
 * Uninstall cleanup for AJAX Availability Check.
 *
 * @package Avch
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete the option that stores the booked dates.
delete_option( 'avch_booked_dates' );
