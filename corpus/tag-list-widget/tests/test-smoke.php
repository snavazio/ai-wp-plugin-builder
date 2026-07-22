<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Tlw
 */

/**
 * Tag List Widget smoke test.
 */
class Tlw_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TLW_VERSION' ), 'TLW_VERSION should be defined.' );
		$this->assertSame( '1.0.0', TLW_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'TLW_Tag_List_Widget' ), 'TLW_Tag_List_Widget class should exist.' );
	}
}
