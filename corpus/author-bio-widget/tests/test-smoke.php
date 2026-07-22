<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Abio
 */

/**
 * Author Bio Widget smoke test.
 */
class Abio_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ABIO_VERSION' ), 'ABIO_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ABIO_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'ABIO_Author_Bio_Widget' ), 'ABIO_Author_Bio_Widget class should exist.' );
	}
}
