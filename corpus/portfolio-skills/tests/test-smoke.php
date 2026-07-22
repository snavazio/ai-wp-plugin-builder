<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Pskl
 */

/**
 * Portfolio Skills smoke test.
 */
class Pskl_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PSKL_VERSION' ), 'PSKL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PSKL_VERSION );
	}

	/**
	 * The work post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'pskl_work' ), 'pskl_work post type should exist.' );
	}

	/**
	 * The skill taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'pskl_skill' ), 'pskl_skill taxonomy should exist.' );
	}

	/**
	 * The work_grid shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'work_grid' ), 'work_grid shortcode should exist.' );
	}
}
