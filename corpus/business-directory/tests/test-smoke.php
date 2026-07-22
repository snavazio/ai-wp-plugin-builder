<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Bdir
 */

/**
 * Business Directory smoke test.
 */
class Bdir_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'BDIR_VERSION' ), 'BDIR_VERSION should be defined.' );
		$this->assertSame( '1.0.0', BDIR_VERSION );
	}

	/**
	 * The business post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'business' ), 'business post type should exist.' );
	}

	/**
	 * The bdir_category taxonomy exists.
	 *
	 * @return void
	 */
	public function test_category_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'bdir_category' ), 'bdir_category taxonomy should exist.' );
	}

	/**
	 * The bdir_region taxonomy exists.
	 *
	 * @return void
	 */
	public function test_region_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'bdir_region' ), 'bdir_region taxonomy should exist.' );
	}

	/**
	 * The directory shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'directory' ), 'directory shortcode should exist.' );
	}
}
