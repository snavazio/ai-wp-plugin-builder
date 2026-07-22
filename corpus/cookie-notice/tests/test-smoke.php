<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cns1
 */

/**
 * Cookie Notice smoke test.
 */
class Cns1_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CNS1_VERSION' ), 'CNS1_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CNS1_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'cns1_register_admin_page' ) );
	}

	/**
	 * The plugin outputs the cookie banner via wp_footer.
	 *
	 * @return void
	 */
	public function test_cookie_banner_output() {
		$this->assertNotFalse( has_action( 'wp_footer', 'cns1_output_cookie_banner' ) );
	}
}
