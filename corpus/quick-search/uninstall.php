<?php
/**
 * Uninstall cleanup for Quick Search.
 *
 * @package Qsrch
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/*
 * TODO(coder): remove this plugin's persistent data here (options, posts, post meta, custom tables).
 * Example: delete_option( 'qsrch_settings' ); and delete any CPT posts you registered.
 * If the plugin stores no data, leave this file with only the guard above.
 */
