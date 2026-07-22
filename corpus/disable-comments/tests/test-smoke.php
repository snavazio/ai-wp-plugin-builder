<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Dcmn
 */

/**
 * Disable Comments smoke test.
 */
class Dcmn_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'DCMN_VERSION' ), 'DCMN_VERSION should be defined.' );
		$this->assertSame( '1.0.0', DCMN_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'dcmn_register_admin_page' ) );
	}
}
