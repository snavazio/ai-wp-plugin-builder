<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Ajaxw
 */

/**
 * AJAX Wishlist smoke test.
 */
class Ajaxw_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'AJAXW_VERSION' ), 'AJAXW_VERSION should be defined.' );
		$this->assertSame( '1.0.0', AJAXW_VERSION );
	}

	/**
	 * The wishlist_button shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'wishlist_button' ) );
	}
}
