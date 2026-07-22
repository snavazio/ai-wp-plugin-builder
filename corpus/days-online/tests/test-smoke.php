<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Dolx
 */

/**
 * Days Online smoke test.
 */
class Dolx_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'DOLX_VERSION' ), 'DOLX_VERSION should be defined.' );
		$this->assertSame( '1.0.0', DOLX_VERSION );
	}

	/**
	 * The days_online shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'days_online' ) );
	}
}
