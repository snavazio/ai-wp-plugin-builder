<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Rmenu
 */

/**
 * Restaurant Menu smoke test.
 */
class Rmenu_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RMENU_VERSION' ), 'RMENU_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RMENU_VERSION );
	}

	/**
	 * The menu_item post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'menu_item' ), 'menu_item post type should exist.' );
	}

	/**
	 * The menu shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'menu' ), 'menu shortcode should exist.' );
	}
}
