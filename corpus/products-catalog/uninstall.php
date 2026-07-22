<?php
/**
 * Uninstall cleanup for Products Catalog.
 *
 * @package Prod
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// No persistent data stored beyond standard WordPress post types and taxonomies.
// WordPress automatically removes CPTs and taxonomies when plugin is uninstalled.
