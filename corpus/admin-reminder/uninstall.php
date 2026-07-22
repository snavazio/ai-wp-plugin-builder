<?php
/**
 * Uninstall cleanup for Admin Reminder.
 *
 * @package Arrem
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// No global data to remove (user meta is per-user and not deleted on uninstall).
