<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Mstg
 */

/**
 * Maintenance Schedule smoke test.
 */
class Mstg_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'MSTG_VERSION' ), 'MSTG_VERSION should be defined.' );
		$this->assertSame( '1.0.0', MSTG_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'mstg_register_admin_page' ) );
	}

	/**
	 * The plugin added the template_redirect hook.
	 *
	 * @return void
	 */
	public function test_template_redirect_hook_added() {
		$this->assertNotFalse( has_action( 'template_redirect', 'mstg_maintenance_schedule' ) );
	}
}
