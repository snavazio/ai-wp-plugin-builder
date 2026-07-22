<?php
/**
 * Uninstall cleanup for Reaction Buttons.
 *
 * @package Reac
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove all post meta associated with reactions.
delete_metadata( 'post', 0, 'reac_reaction', '', true );
