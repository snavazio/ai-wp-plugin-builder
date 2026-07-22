<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cpns
 */

/**
 * Coupons smoke test.
 */
class Cpns_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CPNS_VERSION' ), 'CPNS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CPNS_VERSION );
	}

	/**
	 * The coupon post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'coupon' ), 'coupon post type should exist.' );
	}

	/**
	 * The coupons shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'coupons' ), 'coupons shortcode should exist.' );
	}
}
