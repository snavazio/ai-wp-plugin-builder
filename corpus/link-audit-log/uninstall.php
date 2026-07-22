<?php
/**
 * Uninstall cleanup for Link Audit Log.
 *
 * @package Lalx
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'lalx_audit_log' );
