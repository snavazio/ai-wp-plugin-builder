<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cftx
 */

/**
 * Custom Footer Text smoke test.
 */
class Cftx_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CFTX_VERSION' ), 'CFTX_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CFTX_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'cftx_register_admin_page' ) );
	}
}
