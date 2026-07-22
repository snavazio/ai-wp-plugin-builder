<?php
/**
 * Uninstall cleanup for Popular Posts Widget.
 *
 * @package Ppwgt
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_ppwgt_popular_posts_widget' );
