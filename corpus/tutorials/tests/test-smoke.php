<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Tutly
 */

/**
 * Tutorials smoke test.
 */
class Tutly_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TUTLY_VERSION' ), 'TUTLY_VERSION should be defined.' );
		$this->assertSame( '1.0.0', TUTLY_VERSION );
	}

	/**
	 * The tutorials post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'tutorials' ), 'tutorials post type should exist.' );
	}

	/**
	 * The difficulty taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'difficulty' ), 'difficulty taxonomy should exist.' );
	}

	/**
	 * The tutorials shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'tutorials' ), 'tutorials shortcode should exist.' );
	}
}
