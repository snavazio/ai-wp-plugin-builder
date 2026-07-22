<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cfst
 */

/**
 * Company Footer Settings smoke test.
 */
class Cfst_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CFST_VERSION' ), 'CFST_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CFST_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'cfst_add_settings_page' ) );
	}

	/**
	 * The plugin outputs company name in footer.
	 *
	 * @return void
	 */
	public function test_footer_output() {
		$this->assertNotFalse( has_action( 'wp_footer', 'cfst_output_footer_copyright' ) );
	}
}
