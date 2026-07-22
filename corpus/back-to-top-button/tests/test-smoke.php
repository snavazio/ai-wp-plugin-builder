<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Bttb
 */

/**
 * Back To Top Button smoke test.
 */
class Bttb_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'BTTB_VERSION' ), 'BTTB_VERSION should be defined.' );
		$this->assertSame( '1.0.0', BTTB_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'bttb_register_admin_page' ) );
	}

	/**
	 * The plugin outputs the back-to-top button via wp_footer.
	 *
	 * @return void
	 */
	public function test_button_output() {
		$this->assertNotFalse( has_action( 'wp_footer', 'bttb_output_button' ) );
	}
}
