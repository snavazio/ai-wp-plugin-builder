<?php
/**
 * Uninstall cleanup for AJAX Load Comments.
 *
 * @package Alcp
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/*
 * This plugin stores no persistent data (comments are stored in WordPress core).
 * No cleanup required.
 */
