<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Chmp
 */

/**
 * Custom Head Markup smoke test.
 */
class Chmp_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CHMP_VERSION' ), 'CHMP_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CHMP_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'chmp_add_settings_page' ) );
	}

	/**
	 * The plugin outputs head markup via wp_head.
	 *
	 * @return void
	 */
	public function test_head_markup_output() {
		$this->assertNotFalse( has_action( 'wp_head', 'chmp_output_head_markup' ) );
	}
}
