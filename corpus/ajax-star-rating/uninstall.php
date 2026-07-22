<?php
/**
 * Uninstall cleanup for AJAX Star Rating.
 *
 * @package Arsr
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/*
 * Remove post meta that stores ratings (arsr_total, arsr_count) from all posts.
 * This is safe because post meta is tied to posts and will be removed when posts are deleted.
 */
global $wpdb;
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key IN ( %s, %s )", 'arsr_total', 'arsr_count' ) );
