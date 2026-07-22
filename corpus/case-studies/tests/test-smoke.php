<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cstuds
 */

/**
 * Case Studies smoke test.
 */
class Cstuds_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CSTUDS_VERSION' ), 'CSTUDS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CSTUDS_VERSION );
	}

	/**
	 * The case-study post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'case-study' ), 'case-study post type should exist.' );
	}

	/**
	 * The case_studies shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'case_studies' ), 'case_studies shortcode should exist.' );
	}
}
