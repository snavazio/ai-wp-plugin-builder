<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Alcp
 */

/**
 * AJAX Load Comments smoke test.
 */
class Alcp_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ALCP_VERSION' ), 'ALCP_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ALCP_VERSION );
	}

	/**
	 * The load_comments shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'load_comments' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_alcp_load_comments' ) );
	}
}
