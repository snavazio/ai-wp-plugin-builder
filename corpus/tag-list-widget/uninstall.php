<?php
/**
 * Uninstall cleanup for Tag List Widget.
 *
 * @package Tlw
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_tlw_tag_list_widget' );
