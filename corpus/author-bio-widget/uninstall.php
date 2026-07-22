<?php
/**
 * Uninstall cleanup for Author Bio Widget.
 *
 * @package Abio
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove widget settings option.
delete_option( 'widget_abio_author_bio_widget' );
