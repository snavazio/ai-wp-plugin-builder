<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Winec
 */

/**
 * Wine Catalog smoke test.
 */
class Winec_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'WINEC_VERSION' ), 'WINEC_VERSION should be defined.' );
		$this->assertSame( '1.0.0', WINEC_VERSION );
	}

	/**
	 * The wine post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'wine' ), 'wine post type should exist.' );
	}

	/**
	 * The region taxonomy exists.
	 *
	 * @return void
	 */
	public function test_region_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'region' ), 'region taxonomy should exist.' );
	}

	/**
	 * The varietal taxonomy exists.
	 *
	 * @return void
	 */
	public function test_varietal_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'varietal' ), 'varietal taxonomy should exist.' );
	}

	/**
	 * The wines shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'wines' ), 'wines shortcode should exist.' );
	}
}
