<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Fvman
 */

/**
 * Favicon Manager smoke test.
 */
class Fvman_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'FVMAN_VERSION' ), 'FVMAN_VERSION should be defined.' );
		$this->assertSame( '1.0.0', FVMAN_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'fvman_add_settings_page' ) );
	}

	/**
	 * The plugin outputs favicon via wp_head.
	 *
	 * @return void
	 */
	public function test_head_output_registered() {
		$this->assertNotFalse( has_action( 'wp_head', 'fvman_output_favicon' ) );
	}
}
