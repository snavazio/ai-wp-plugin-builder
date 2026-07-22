<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Rent
 */

/**
 * Rentals smoke test.
 */
class Rent_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RENT_VERSION' ), 'RENT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RENT_VERSION );
	}

	/**
	 * The rental post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'rental' ), 'rental post type should exist.' );
	}

	/**
	 * The rentals shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'rentals' ), 'rentals shortcode should exist.' );
	}
}
