<?php
/**
 * Uninstall cleanup for Daily Quote Rotator.
 *
 * @package Daily
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'daily_quote_of_day' );
