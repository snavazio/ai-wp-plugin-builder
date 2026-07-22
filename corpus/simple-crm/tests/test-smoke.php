<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Scrm
 */

/**
 * Simple CRM smoke test.
 */
class Scrm_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SCRM_VERSION' ), 'SCRM_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SCRM_VERSION );
	}

	/**
	 * The contact post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'scrm_contact' ), 'scrm_contact post type should exist.' );
	}

	/**
	 * The status taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'scrm_status' ), 'scrm_status taxonomy should exist.' );
	}

	/**
	 * The contacts shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'contacts' ), 'contacts shortcode should exist.' );
	}
}
