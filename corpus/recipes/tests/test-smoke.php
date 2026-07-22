<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Rcpr
 */

/**
 * Recipes smoke test.
 */
class Rcpr_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RCPR_VERSION' ), 'RCPR_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RCPR_VERSION );
	}

	/**
	 * The recipe post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'recipe' ), 'recipe post type should exist.' );
	}

	/**
	 * The recipe_card shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'recipe_card' ), 'recipe_card shortcode should exist.' );
	}
}
