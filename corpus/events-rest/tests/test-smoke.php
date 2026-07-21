<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Evtr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Events REST smoke test.
 */
class Evtr_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'EVTR_VERSION' ), 'EVTR_VERSION should be defined.' );
		$this->assertSame( '1.0.0', EVTR_VERSION );
	}

	/**
	 * Test that the evtr_event post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'evtr_event' ), 'evtr_event post type should be registered.' );
	}

	/**
	 * Test that the REST API route is registered.
	 *
	 * @return void
	 */
	public function test_rest_route_exists() {
		$routes = rest_get_server()->get_routes();
		$this->assertArrayHasKey( '/events/v1/events', $routes, 'REST route /events/v1/events should be registered.' );
	}

	/**
	 * Test that the post type is public.
	 *
	 * @return void
	 */
	public function test_post_type_is_public() {
		$post_type_object = get_post_type_object( 'evtr_event' );
		$this->assertNotNull( $post_type_object, 'Post type object should exist.' );
		$this->assertTrue( $post_type_object->public, 'Post type should be public.' );
	}

	/**
	 * Test that the post type supports title and editor.
	 *
	 * @return void
	 */
	public function test_post_type_supports() {
		$this->assertTrue( post_type_supports( 'evtr_event', 'title' ), 'Post type should support title.' );
		$this->assertTrue( post_type_supports( 'evtr_event', 'editor' ), 'Post type should support editor.' );
	}
}
