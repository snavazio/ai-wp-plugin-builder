<?php
/**
 * GitHub publishing settings: account, access token (server-side only, never rendered back), and whether new
 * repositories are private.
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read access to the saved GitHub settings.
 */
class Aiwpb_Github {

	const OWNER_OPTION   = 'aiwpb_github_owner';
	const TOKEN_OPTION   = 'aiwpb_github_token';
	const PRIVATE_OPTION = 'aiwpb_github_private';

	/**
	 * GitHub account (user or organisation) repositories are created under.
	 *
	 * @return string
	 */
	public static function owner() {
		$owner = (string) get_option( self::OWNER_OPTION, 'snavazio' );
		return '' !== $owner ? $owner : 'snavazio';
	}

	/**
	 * The saved token, or ''. Server-side use only.
	 *
	 * @return string
	 */
	public static function token() {
		return (string) get_option( self::TOKEN_OPTION, '' );
	}

	/**
	 * Whether new repositories are created private (default yes).
	 *
	 * @return bool
	 */
	public static function is_private() {
		return '0' !== (string) get_option( self::PRIVATE_OPTION, '1' );
	}

	/**
	 * Masked token for display: last 4 characters only.
	 *
	 * @return string
	 */
	public static function masked_token() {
		$t = self::token();
		return '' === $t ? '' : '••••' . substr( $t, -4 );
	}
}
