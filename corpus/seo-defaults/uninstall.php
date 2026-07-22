<?php
/**
 * Uninstall cleanup for SEO Defaults.
 *
 * @package Seod
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete the default meta description option.
delete_option( 'seod_default_meta_description' );
