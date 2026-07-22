<?php
/**
 * Uninstall cleanup for Recent Comments Widget.
 *
 * @package Rcws
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_rcws_recent_comments_widget' );
