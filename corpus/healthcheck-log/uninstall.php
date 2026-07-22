<?php
/**
 * Uninstall cleanup for Healthcheck Log.
 *
 * @package Hlck
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'hlck_healthcheck_data' );
