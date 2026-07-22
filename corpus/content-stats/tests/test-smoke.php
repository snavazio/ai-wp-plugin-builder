<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cstw
 */

/**
 * Content Stats smoke test.
 */
class Cstw_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CSTW_VERSION' ), 'CSTW_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CSTW_VERSION );
	}

	/**
	 * The Content Stats dashboard widget is registered.
	 *
	 * @return void
	 */
	public function test_dashboard_widget_registered() {
		$this->assertNotFalse( has_action( 'wp_dashboard_setup', 'cstw_register_dashboard_widget' ) );
	}
}
