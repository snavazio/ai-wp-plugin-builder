<?php
/**
 * Uninstall cleanup for Custom Head Markup.
 *
 * @package Chmp
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the head markup option.
delete_option( 'chmp_head_markup' );
