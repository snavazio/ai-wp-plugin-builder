<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Kbpl
 */

/**
 * Knowledge Base Plugin smoke test.
 */
class Kbpl_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'KBPL_VERSION' ), 'KBPL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', KBPL_VERSION );
	}

	/**
	 * The article post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'kbpl_article' ), 'kbpl_article post type should exist.' );
	}

	/**
	 * The topic taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'kbpl_topic' ), 'kbpl_topic taxonomy should exist.' );
	}

	/**
	 * The kb_articles shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'kb_articles' ), 'kb_articles shortcode should exist.' );
	}
}
