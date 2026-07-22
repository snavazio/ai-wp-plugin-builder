<?php
/**
 * Uninstall cleanup for Social Links Widget.
 *
 * @package Slwgt
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove the social links option.
delete_option( 'slwgt_social_links' );
