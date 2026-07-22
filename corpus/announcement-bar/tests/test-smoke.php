<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Annbar
 */

/**
 * Announcement Bar smoke test.
 */
class Annbar_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ANNBAR_VERSION' ), 'ANNBAR_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ANNBAR_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'annbar_register_admin_page' ) );
	}

	/**
	 * The plugin outputs the announcement bar via wp_body_open.
	 *
	 * @return void
	 */
	public function test_bar_output() {
		$this->assertNotFalse( has_action( 'wp_body_open', 'annbar_output_bar' ) );
	}
}
