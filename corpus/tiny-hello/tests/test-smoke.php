<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Thl
 */

/**
 * Tiny Hello smoke test.
 */
class Thl_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'THL_VERSION' ), 'THL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', THL_VERSION );
	}

	/**
	 * The tiny_hello shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'tiny_hello' ) );
	}
}
