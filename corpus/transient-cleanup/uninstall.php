<?php
/**
 * Uninstall cleanup for Transient Cleanup.
 *
 * @package Tcleanup
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// No persistent data stored by this plugin (only cleans up external options).
