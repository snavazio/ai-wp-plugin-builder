<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Acv1
 */

/**
 * AJAX Comment Vote smoke test.
 */
class Acv1_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ACV1_VERSION' ), 'ACV1_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ACV1_VERSION );
	}

	/**
	 * The AJAX handler is registered for logged-in users.
	 *
	 * @return void
	 */
	public function test_ajax_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_acv1_vote' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_acv1_vote' ) );
	}
}
