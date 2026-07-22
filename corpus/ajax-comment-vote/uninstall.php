<?php
/**
 * Uninstall cleanup for AJAX Comment Vote.
 *
 * @package Acv1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/*
 * Remove comment meta that stores vote counts.
 */
global $wpdb;
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->commentmeta WHERE meta_key = %s", '_acv1_vote_count' ) );
