<?php
/**
 * Uninstall cleanup for Mini CTA Widget.
 *
 * @package Mctw
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_mctw_cta_widget' );
