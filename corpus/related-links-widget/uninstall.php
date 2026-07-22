<?php
/**
 * Uninstall cleanup for Related Links Widget.
 *
 * @package Rlwgt
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_rlwgt_related_links_widget' );
