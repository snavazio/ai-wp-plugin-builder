<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Crs1
 */

/**
 * Courses smoke test.
 */
class Crs1_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CRS1_VERSION' ), 'CRS1_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CRS1_VERSION );
	}

	/**
	 * The course post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'crs1_course' ), 'crs1_course post type should exist.' );
	}

	/**
	 * The subject taxonomy exists.
	 *
	 * @return void
	 */
	public function test_subject_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'crs1_subject' ), 'crs1_subject taxonomy should exist.' );
	}

	/**
	 * The level taxonomy exists.
	 *
	 * @return void
	 */
	public function test_level_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'crs1_level' ), 'crs1_level taxonomy should exist.' );
	}

	/**
	 * The courses shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'courses' ), 'courses shortcode should exist.' );
	}
}
