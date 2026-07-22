<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Sttop
 */

/**
 * Scroll To Top smoke test.
 */
class Sttop_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'STTOP_VERSION' ), 'STTOP_VERSION should be defined.' );
		$this->assertSame( '1.0.0', STTOP_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'sttop_register_admin_page' ) );
	}

	/**
	 * The plugin outputs the scroll to top button via wp_footer.
	 *
	 * @return void
	 */
	public function test_scroll_to_top_output() {
		$this->assertNotFalse( has_action( 'wp_footer', 'sttop_output_scroll_to_top' ) );
	}
}
