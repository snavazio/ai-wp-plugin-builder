<?php
/**
 * Uninstall cleanup for Reminder Log.
 *
 * @package Rlog
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'rlog_scheduled_posts' );
