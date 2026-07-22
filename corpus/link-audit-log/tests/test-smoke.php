<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Lalx
 */

/**
 * Link Audit Log smoke test.
 */
class Lalx_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'LALX_VERSION' ), 'LALX_VERSION should be defined.' );
		$this->assertSame( '1.0.0', LALX_VERSION );
	}

	/**
	 * The link_audit shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'link_audit' ) );
	}
}
