<?php
/**
 * Uninstall cleanup for Contact Card Widget.
 *
 * @package Ccw1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_ccw1_contact_card_widget' );
