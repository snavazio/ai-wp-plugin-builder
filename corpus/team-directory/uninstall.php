<?php
/**
 * Uninstall cleanup for Team Directory.
 *
 * @package Tdir
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// No plugin-specific options stored; remove only if we had custom options.
// Since we don't store options, no action needed.
