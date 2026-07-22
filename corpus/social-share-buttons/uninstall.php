<?php
/**
 * Uninstall cleanup for Social Share Buttons.
 *
 * @package Ssbs
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete social networks option.
delete_option( 'ssbs_social_networks' );
