<?php
/**
 * Uninstall cleanup for Movie Reviews.
 *
 * @package Mrev
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// No plugin-specific data stored in options or custom tables.
// Movie posts and taxonomies are user content and should not be removed.
// The uninstall.php file is required by WordPress but does not need to remove data.
