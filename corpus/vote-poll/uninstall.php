<?php
/**
 * Uninstall cleanup for Vote Poll.
 *
 * @package Vtpol
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all options with the prefix 'vtpol_poll_'.
global $wpdb;
$options = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s", 'vtpol_poll_%' ) );
foreach ( $options as $option ) {
	delete_option( $option );
}
