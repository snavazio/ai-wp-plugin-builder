<?php
/**
 * Uninstall cleanup for Featured Post Widget.
 *
 * @package Fpw1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_fpw1_featured_post_widget' );
