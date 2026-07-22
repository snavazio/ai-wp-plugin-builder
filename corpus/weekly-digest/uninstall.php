<?php
/**
 * Uninstall cleanup for Weekly Digest.
 *
 * @package Wkdig
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'wkdig_weekly_post_count' );
