<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Caf1
 */

/**
 * Custom Admin Footer smoke test.
 */
class Caf1_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CAF1_VERSION' ), 'CAF1_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CAF1_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'caf1_register_admin_page' ) );
	}

	/**
	 * The plugin replaces the admin footer text.
	 *
	 * @return void
	 */
	public function test_admin_footer_text_replaced() {
		$this->assertNotFalse( has_filter( 'admin_footer_text', 'caf1_footer_text' ) );
	}
}
