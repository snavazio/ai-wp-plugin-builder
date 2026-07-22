<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Brpl
 */

/**
 * Book Reviews smoke test.
 */
class Brpl_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'BRPL_VERSION' ), 'BRPL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', BRPL_VERSION );
	}

	/**
	 * The book_review post type exists.
	 *
	 * @return void
	 */
	public function test_book_review_post_type_exists() {
		$this->assertTrue( post_type_exists( 'book_review' ), 'Book Review post type should exist.' );
	}
}
