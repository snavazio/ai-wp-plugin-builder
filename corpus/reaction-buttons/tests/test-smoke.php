<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Reac
 */

/**
 * Reaction Buttons smoke test.
 */
class Reac_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'REAC_VERSION' ), 'REAC_VERSION should be defined.' );
		$this->assertSame( '1.0.0', REAC_VERSION );
	}

	/**
	 * The reactions shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'reactions' ) );
	}

	/**
	 * The AJAX handler is registered for logged-in users.
	 *
	 * @return void
	 */
	public function test_ajax_handler_registered() {
		$this->assertTrue( has_action( 'wp_ajax_reac_reaction_click' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_handler_registered() {
		$this->assertTrue( has_action( 'wp_ajax_nopriv_reac_reaction_click' ) );
	}
}
