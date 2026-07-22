<?php
/**
 * Uninstall cleanup for Recent Products Widget.
 *
 * @package Rpwgt
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_rpwgt_recent_products_widget' );
