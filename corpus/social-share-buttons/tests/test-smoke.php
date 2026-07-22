<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Ssbs
 */

/**
 * Social Share Buttons smoke test.
 */
class Ssbs_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SSBS_VERSION' ), 'SSBS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SSBS_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'ssbs_add_settings_page' ) );
	}

	/**
	 * The plugin appends share links to content on single posts.
	 *
	 * @return void
	 */
	public function test_content_filter_registered() {
		$this->assertNotFalse( has_filter( 'the_content', 'ssbs_append_share_links' ) );
	}
}
