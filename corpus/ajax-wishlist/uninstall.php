<?php
/**
 * Uninstall cleanup for AJAX Wishlist.
 *
 * @package Ajaxw
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;
$options = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s", 'ajaxw_wishlist_%' ) );
foreach ( $options as $option ) {
	delete_option( $option );
}
