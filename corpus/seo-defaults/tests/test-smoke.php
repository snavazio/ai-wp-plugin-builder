<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Seod
 */

/**
 * SEO Defaults smoke test.
 */
class Seod_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SEOD_VERSION' ), 'SEOD_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SEOD_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'seod_add_settings_page' ) );
	}

	/**
	 * The plugin outputs meta description via wp_head.
	 *
	 * @return void
	 */
	public function test_head_markup_output() {
		$this->assertNotFalse( has_action( 'wp_head', 'seo_default_meta_description' ) );
	}
}
