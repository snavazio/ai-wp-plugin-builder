<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package C404m
 */

/**
 * Custom 404 Message smoke test.
 */
class C404m_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'C404M_VERSION' ), 'C404M_VERSION should be defined.' );
		$this->assertSame( '1.0.0', C404M_VERSION );
	}

	/**
	 * The plugin registered the admin menu.
	 *
	 * @return void
	 */
	public function test_admin_menu_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'c404m_add_404_settings' ) );
	}

	/**
	 * The plugin added the content filter for 404 pages.
	 *
	 * @return void
	 */
	public function test_content_filter_added() {
		$this->assertNotFalse( has_filter( 'the_content', 'c404m_render_404' ) );
	}
}
