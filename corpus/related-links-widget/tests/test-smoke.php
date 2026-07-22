<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Rlwgt
 */

/**
 * Related Links Widget smoke test.
 */
class Rlwgt_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RLWGT_VERSION' ), 'RLWGT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RLWGT_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'Rlwgt_Related_Links_Widget' ), 'Rlwgt_Related_Links_Widget class should exist.' );
	}
}
