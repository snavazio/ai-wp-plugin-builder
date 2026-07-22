<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Staff
 */

/**
 * Staff Directory smoke test.
 */
class Staff_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'STAFF_VERSION' ), 'STAFF_VERSION should be defined.' );
		$this->assertSame( '1.0.0', STAFF_VERSION );
	}

	/**
	 * The staff post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'staff' ), 'staff post type should exist.' );
	}

	/**
	 * The staff_list shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'staff_list' ), 'staff_list shortcode should exist.' );
	}
}
