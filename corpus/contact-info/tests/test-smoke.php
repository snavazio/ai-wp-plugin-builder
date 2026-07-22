<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cinfo
 */

/**
 * Contact Info smoke test.
 */
class Cinfo_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CINFO_VERSION' ), 'CINFO_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CINFO_VERSION );
	}

	/**
	 * The contact_info shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertNotFalse( shortcode_exists( 'contact_info' ), 'The contact_info shortcode should exist.' );
	}
}
