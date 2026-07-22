<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Lmpa
 */

/**
 * Load More Posts smoke test.
 */
class Lmpa_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'LMPA_VERSION' ), 'LMPA_VERSION should be defined.' );
		$this->assertSame( '1.0.0', LMPA_VERSION );
	}

	/**
	 * The load_more shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'load_more' ) );
	}
}
