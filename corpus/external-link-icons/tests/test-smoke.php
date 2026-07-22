<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Elink
 */

/**
 * External Link Icons smoke test.
 */
class Elink_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ELINK_VERSION' ), 'ELINK_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ELINK_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'elink_register_admin_page' ) );
	}

	/**
	 * The plugin added the content filter.
	 *
	 * @return void
	 */
	public function test_content_filter_added() {
		$this->assertNotFalse( has_filter( 'the_content', 'elink_add_external_link_icons' ) );
	}
}
