<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Wkdig
 */

/**
 * Weekly Digest smoke test.
 */
class Wkdig_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'WKDIG_VERSION' ), 'WKDIG_VERSION should be defined.' );
		$this->assertSame( '1.0.0', WKDIG_VERSION );
	}

	/**
	 * The weekly_digest shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'weekly_digest' ) );
	}
}
