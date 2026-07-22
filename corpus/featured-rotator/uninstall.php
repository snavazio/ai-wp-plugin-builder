<?php
/**
 * Uninstall cleanup for Featured Rotator.
 *
 * @package Frrot
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'frrot_featured_post_id' );
