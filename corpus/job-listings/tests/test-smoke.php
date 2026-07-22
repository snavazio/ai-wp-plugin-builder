<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Jobl
 */

/**
 * Job Listings smoke test.
 */
class Jobl_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'JOBL_VERSION' ), 'JOBL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', JOBL_VERSION );
	}

	/**
	 * The job post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'job' ), 'job post type should exist.' );
	}

	/**
	 * The jobs shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'jobs' ), 'jobs shortcode should exist.' );
	}
}
