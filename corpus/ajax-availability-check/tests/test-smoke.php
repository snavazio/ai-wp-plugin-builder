<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Avch
 */

/**
 * AJAX Availability Check smoke test.
 */
class Avch_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'AVCH_VERSION' ), 'AVCH_VERSION should be defined.' );
		$this->assertSame( '1.0.0', AVCH_VERSION );
	}

	/**
	 * The availability shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertNotFalse( shortcode_exists( 'availability' ) );
	}
}
