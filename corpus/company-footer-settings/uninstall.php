<?php
/**
 * Uninstall cleanup for Company Footer Settings.
 *
 * @package Cfst
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete company name option.
delete_option( 'cfst_company_name' );
