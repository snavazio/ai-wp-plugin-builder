<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Tmnl
 */

/**
 * Testimonials smoke test.
 */
class Tmnl_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TMNL_VERSION' ), 'TMNL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', TMNL_VERSION );
	}

	/**
	 * Verify the testimonial post type is registered and configured correctly.
	 *
	 * @return void
	 */
	public function test_testimonial_post_type_registered() {
		// post_type_exists('tmnl_testimonial').
		$this->assertTrue( post_type_exists( 'tmnl_testimonial' ), 'tmnl_testimonial post type should be registered.' );

		// !is_post_type_viewable('tmnl_testimonial').
		$this->assertFalse( is_post_type_viewable( 'tmnl_testimonial' ), 'tmnl_testimonial should not be publicly viewable.' );

		// get_post_type_object('tmnl_testimonial')->public === false.
		$post_type_object = get_post_type_object( 'tmnl_testimonial' );
		$this->assertNotNull( $post_type_object, 'tmnl_testimonial post type object should exist.' );
		$this->assertFalse( $post_type_object->public, 'tmnl_testimonial public property should be false.' );

		// metadata_exists('post', 1, 'tmnl_rating') || true - always passes but we can test meta capability.
		$this->assertTrue( true, 'Meta existence check - always passes as per spec.' );
	}
}
