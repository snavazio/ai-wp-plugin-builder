<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Arrem
 */

/**
 * Admin Reminder smoke test.
 */
class Arrem_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ARREM_VERSION' ), 'ARREM_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ARREM_VERSION );
	}

	/*
	 * TODO(coder): add feature-specific assertions based on the SPEC's smokeAssertions.
	 * IMPORTANT — assertTrue() is STRICT (assertTrue(10) FAILS):
	 *   - Real booleans use assertTrue():   assertTrue( post_type_exists( 'arrem_item' ) );
	 *                                       assertTrue( taxonomy_exists( 'arrem_type' ) );
	 *                                       assertTrue( shortcode_exists( 'your_shortcode' ) );
	 *   - has_action()/has_filter() return an int priority or false — use assertNotFalse():
	 *                                       assertNotFalse( has_action( 'rest_api_init' ) );
	 * Do not assert rendered markup or option values that need fixtures.
	 */
}
