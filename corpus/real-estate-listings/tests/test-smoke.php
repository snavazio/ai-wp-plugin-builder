<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Reli
 */

/**
 * Real Estate Listings smoke test.
 */
class Reli_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RELI_VERSION' ), 'RELI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RELI_VERSION );
	}

	/**
	 * The property post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'property' ), 'property post type should exist.' );
	}

	/**
	 * The properties shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'properties' ), 'properties shortcode should exist.' );
	}
}
