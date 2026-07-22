<?php
/**
 * Uninstall cleanup for Call To Action Widget.
 *
 * @package Ctaw
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_ctaw_cta_widget' );
