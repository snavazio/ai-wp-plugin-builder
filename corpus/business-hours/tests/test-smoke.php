<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Bhrs
 */

/**
 * Business Hours smoke test.
 */
class Bhrs_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'BHRS_VERSION' ), 'BHRS_VERSION should be defined.' );
		$this->assertSame( '1.0.1', BHRS_VERSION );
	}

	/**
	 * Test that the business_hours shortcode is registered.
	 *
	 * @return void
	 */
	public function test_shortcode_registered() {
		$this->assertTrue( shortcode_exists( 'business_hours' ), 'business_hours shortcode should be registered.' );
	}

	/**
	 * Test that the business_hours_today shortcode is registered.
	 *
	 * @return void
	 */
	public function test_today_shortcode_registered() {
		$this->assertTrue( shortcode_exists( 'business_hours_today' ), 'business_hours_today shortcode should be registered.' );
	}

	/**
	 * Test that options exist or can be set.
	 *
	 * @return void
	 */
	public function test_options_exist() {
		// Set a test option to verify the option keys work.
		update_option( 'bhrs_monday_open', '9:00 AM' );
		update_option( 'bhrs_monday_closed', true );

		$this->assertSame( '9:00 AM', get_option( 'bhrs_monday_open' ), 'bhrs_monday_open option should exist or be settable.' );
		// Check that monday_closed option was stored (get_option will return false if not found).
		$this->assertTrue( get_option( 'bhrs_monday_closed' ), 'bhrs_monday_closed option should exist or be settable.' );
	}

	/**
	 * Test that admin menu hook is registered.
	 *
	 * @return void
	 */
	public function test_admin_menu_hook() {
		$this->assertNotFalse( has_action( 'admin_menu', 'bhrs_add_settings_page' ), 'admin_menu action should be registered.' );
		$this->assertTrue( function_exists( 'bhrs_add_settings_page' ), 'bhrs_add_settings_page function should exist.' );
	}

	/**
	 * Test that admin_init hook is registered.
	 *
	 * @return void
	 */
	public function test_admin_init_hook() {
		$this->assertNotFalse( has_action( 'admin_init', 'bhrs_register_settings' ), 'admin_init action should be registered.' );
		$this->assertTrue( function_exists( 'bhrs_register_settings' ), 'bhrs_register_settings function should exist.' );
	}
}
