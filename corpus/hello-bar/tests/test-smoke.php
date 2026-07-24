<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Hbar
 */

/**
 * Hello Bar smoke test.
 */
class Hbar_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'HBAR_VERSION' ), 'HBAR_VERSION should be defined.' );
		$this->assertSame( '1.0.0', HBAR_VERSION );
	}

	/**
	 * The hello_bar shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'hello_bar' ) );
	}
}
