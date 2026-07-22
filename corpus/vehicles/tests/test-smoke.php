<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Vclt
 */

/**
 * Vehicles smoke test.
 */
class Vclt_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'VCLT_VERSION' ), 'VCLT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', VCLT_VERSION );
	}

	/**
	 * The vehicle post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'vehicle' ), 'vehicle post type should exist.' );
	}

	/**
	 * The vehicles shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'vehicles' ), 'vehicles shortcode should exist.' );
	}
}
