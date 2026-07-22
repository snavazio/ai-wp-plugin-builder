<?php
/**
 * Uninstall cleanup for Quote Widget.
 *
 * @package Qwid
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_qwid_quote_widget' );
