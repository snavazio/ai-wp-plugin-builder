<?php
/**
 * WordPress.org publishing settings. Only the username lives here; the SVN password is set on the builder
 * service machine (never stored in WordPress).
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read access to the saved WordPress.org settings.
 */
class Aiwpb_Wporg {

	const USER_OPTION = 'aiwpb_wporg_username';

	/**
	 * WordPress.org username used as the readme "Contributors".
	 *
	 * @return string
	 */
	public static function username() {
		$u = (string) get_option( self::USER_OPTION, 'snavazio' );
		return '' !== $u ? $u : 'snavazio';
	}
}
