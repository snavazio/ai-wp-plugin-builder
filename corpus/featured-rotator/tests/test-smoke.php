<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Frrot
 */

/**
 * Featured Rotator smoke test.
 */
class Frrot_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'FRROT_VERSION' ), 'FRROT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', FRROT_VERSION );
	}

	/**
	 * The featured_post shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'featured_post' ) );
	}
}
