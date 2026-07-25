<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cnote
 */

/**
 * Color Note smoke test.
 */
class Cnote_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CNOTE_VERSION' ), 'CNOTE_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CNOTE_VERSION );
	}

	/**
	 * The color_note shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'color_note' ) );
	}
}
