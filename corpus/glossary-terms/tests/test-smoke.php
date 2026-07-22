<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Gterm
 */

/**
 * Glossary Terms smoke test.
 */
class Gterm_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'GTERM_VERSION' ), 'GTERM_VERSION should be defined.' );
		$this->assertSame( '1.0.0', GTERM_VERSION );
	}

	/**
	 * The gterm_glossary_term post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'gterm_glossary_term' ), 'gterm_glossary_term post type should exist.' );
	}

	/**
	 * The glossary shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'glossary' ), 'glossary shortcode should exist.' );
	}
}
