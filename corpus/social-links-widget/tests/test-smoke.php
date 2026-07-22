<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Slwgt
 */

/**
 * Social Links Widget smoke test.
 */
class Slwgt_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SLWGT_VERSION' ), 'SLWGT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SLWGT_VERSION );
	}

	/**
	 * The plugin registered the admin page.
	 *
	 * @return void
	 */
	public function test_admin_page_registered() {
		$this->assertNotFalse( has_action( 'admin_menu', 'slwgt_register_admin_page' ) );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'SLWGT_Social_Links_Widget' ), 'SLWGT_Social_Links_Widget class should exist.' );
	}
}
