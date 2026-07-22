<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Bkslot
 */

/**
 * Booking Slots smoke test.
 */
class Bkslot_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'BKSLOT_VERSION' ), 'BKSLOT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', BKSLOT_VERSION );
	}

	/**
	 * The slot post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'slot' ), 'The slot post type should exist.' );
	}

	/**
	 * The slots shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'slots' ), 'The slots shortcode should exist.' );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_book_slot' ) );
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_book_slot' ) );
	}
}
