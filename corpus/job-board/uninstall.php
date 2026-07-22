<?php
/**
 * Uninstall cleanup for Job Board.
 *
 * @package Jbrd
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// No plugin-specific options stored; remove only if we had custom options.
// Since we don't store options, no action needed.
