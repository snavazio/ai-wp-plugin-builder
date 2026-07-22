<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Pgui
 */

/**
 * Plant Guide smoke test.
 */
class Pgui_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PGUI_VERSION' ), 'PGUI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PGUI_VERSION );
	}

	/**
	 * The plant post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'pgui_plant' ), 'pgui_plant post type should exist.' );
	}

	/**
	 * The family taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'pgui_family' ), 'pgui_family taxonomy should exist.' );
	}

	/**
	 * The plants shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'plants' ), 'plants shortcode should exist.' );
	}
}
