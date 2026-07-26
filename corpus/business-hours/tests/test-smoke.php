<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Bhrs
 */

/**
 * Business Hours smoke test.
 */
class Bhrs_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'BHRS_VERSION' ), 'BHRS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', BHRS_VERSION );
	}

	/**
	 * The business_hours shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'business_hours' ) );
	}
}
