<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Help
 */

/**
 * Help Desk smoke test.
 */
class Help_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'HELP_VERSION' ), 'HELP_VERSION should be defined.' );
		$this->assertSame( '1.0.0', HELP_VERSION );
	}

	/**
	 * The help_ticket post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'help_ticket' ), 'help_ticket post type should exist.' );
	}

	/**
	 * The help_status taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'help_status' ), 'help_status taxonomy should exist.' );
	}

	/**
	 * The help_tickets shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'help_tickets' ), 'help_tickets shortcode should exist.' );
	}

	/**
	 * The help_tickets REST endpoint exists.
	 *
	 * @return void
	 */
	public function test_rest_endpoint_exists() {
		$routes = rest_get_server()->get_routes();
		$this->assertArrayHasKey( '/help/v1/tickets', $routes, 'help_tickets REST endpoint should exist.' );
	}
}
