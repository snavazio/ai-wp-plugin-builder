<?php
/**
 * Uninstall cleanup for Countdown Widget.
 *
 * @package Cdwgt
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_cdwgt_widget' );
