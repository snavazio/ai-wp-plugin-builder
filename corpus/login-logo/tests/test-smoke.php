<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Lgnl
 */

/**
 * Login Logo smoke test.
 */
class Lgnl_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'LGNL_VERSION' ), 'LGNL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', LGNL_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'lgnl_register_admin_page' ) );
	}

	/**
	 * The plugin outputs login logo CSS via login_head.
	 *
	 * @return void
	 */
	public function test_login_logo_css_output() {
		$this->assertNotFalse( has_action( 'login_head', 'lgnl_output_login_logo_css' ) );
	}
}
