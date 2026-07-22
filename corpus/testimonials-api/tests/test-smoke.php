<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Tstm
 */

/**
 * Testimonials API smoke test.
 */
class Tstm_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TSTM_VERSION' ), 'TSTM_VERSION should be defined.' );
		$this->assertSame( '1.0.0', TSTM_VERSION );
	}

	/**
	 * The testimonial post type exists.
	 *
	 * @return void
	 */
	public function test_testimonial_post_type_exists() {
		$this->assertTrue( post_type_exists( 'tstm_testimonial' ), 'Testimonial post type should exist.' );
	}
}
