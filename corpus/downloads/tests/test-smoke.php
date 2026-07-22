<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Dlm1
 */

/**
 * Downloads smoke test.
 */
class Dlm1_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'DLM1_VERSION' ), 'DLM1_VERSION should be defined.' );
		$this->assertSame( '1.0.0', DLM1_VERSION );
	}

	/**
	 * The download post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'download' ), 'download post type should exist.' );
	}

	/**
	 * The downloads shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'downloads' ), 'downloads shortcode should exist.' );
	}
}
