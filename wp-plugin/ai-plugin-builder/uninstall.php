<?php
/**
 * Uninstall cleanup for AI Plugin Builder — remove stored settings.
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

foreach ( array( 'aiwpb_backend_url', 'aiwpb_api_key', 'aiwpb_default_engine', 'aiwpb_local_model' ) as $aiwpb_option ) {
	delete_option( $aiwpb_option );
}
