<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Prtf
 */

/**
 * Portfolio smoke test.
 */
class Prtf_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PRTF_VERSION' ), 'PRTF_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PRTF_VERSION );
	}

	/**
	 * Test that the custom post type is registered.
	 *
	 * @return void
	 */
	public function test_post_type_registered() {
		$this->assertTrue( post_type_exists( 'prtf_portfolio' ), 'Post type prtf_portfolio should exist.' );
	}

	/**
	 * Test that the taxonomy is registered.
	 *
	 * @return void
	 */
	public function test_taxonomy_registered() {
		$this->assertTrue( taxonomy_exists( 'prtf_project_type' ), 'Taxonomy prtf_project_type should exist.' );
	}

	/**
	 * Test that the shortcode is registered.
	 *
	 * @return void
	 */
	public function test_shortcode_registered() {
		$this->assertTrue( shortcode_exists( 'portfolio' ), 'Shortcode [portfolio] should exist.' );
	}

	/**
	 * Test that the AJAX action for logged-in users is registered.
	 *
	 * @return void
	 */
	public function test_ajax_action_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_prtf_load_more' ), 'AJAX action wp_ajax_prtf_load_more should be registered.' );
	}

	/**
	 * Test that the AJAX action for logged-out users is registered.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_action_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_prtf_load_more' ), 'AJAX action wp_ajax_nopriv_prtf_load_more should be registered.' );
	}
}
