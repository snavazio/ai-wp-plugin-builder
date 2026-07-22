<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cyrs
 */

/**
 * Current Year smoke test.
 */
class Cyrs_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CYRS_VERSION' ), 'CYRS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CYRS_VERSION );
	}

	/**
	 * The current_year shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'current_year' ) );
	}
}
