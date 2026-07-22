<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Ctabtn
 */

/**
 * CTA Button smoke test.
 */
class Ctabtn_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CTABTN_VERSION' ), 'CTABTN_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CTABTN_VERSION );
	}

	/**
	 * The cta_button shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'cta_button' ) );
	}
}
