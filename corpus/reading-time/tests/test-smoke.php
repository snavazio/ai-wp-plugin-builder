<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Rtmin
 */

/**
 * Reading Time smoke test.
 */
class Rtmin_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RTMIN_VERSION' ), 'RTMIN_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RTMIN_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'rtmin_register_admin_page' ) );
	}

	/**
	 * The plugin added the content filter.
	 *
	 * @return void
	 */
	public function test_content_filter_added() {
		$this->assertNotFalse( has_filter( 'the_content', 'rtmin_add_reading_time' ) );
	}
}
