<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Vtpol
 */

/**
 * Vote Poll smoke test.
 */
class Vtpol_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'VTPOL_VERSION' ), 'VTPOL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', VTPOL_VERSION );
	}

	/**
	 * The poll shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'poll' ) );
	}

	/**
	 * The AJAX handler is registered for logged-in users.
	 *
	 * @return void
	 */
	public function test_ajax_handler_registered() {
		$this->assertTrue( has_action( 'wp_ajax_vtpol_vote_poll' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_handler_registered() {
		$this->assertTrue( has_action( 'wp_ajax_nopriv_vtpol_vote_poll' ) );
	}
}
