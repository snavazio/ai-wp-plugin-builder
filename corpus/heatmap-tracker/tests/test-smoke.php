<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Hmtrk
 */

/**
 * Heatmap Tracker smoke test.
 */
class Hmtrk_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'HMTRK_VERSION' ), 'HMTRK_VERSION should be defined.' );
		$this->assertSame( '1.0.0', HMTRK_VERSION );
	}

	/**
	 * The events table and DB version option exist.
	 *
	 * @return void
	 */
	public function test_table_and_option() {
		global $wpdb;
		$table = $wpdb->prefix . 'hmtrk_events';
		$this->assertSame( $table, $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) );
		$this->assertNotFalse( get_option( 'hmtrk_db_version' ) );
	}

	/**
	 * REST routes are registered.
	 *
	 * @return void
	 */
	public function test_rest_routes() {
		do_action( 'rest_api_init' );
		$routes = rest_get_server()->get_routes();
		$this->assertArrayHasKey( '/hmtrk/v1/events', $routes );
		$this->assertArrayHasKey( '/hmtrk/v1/results', $routes );
	}

	/**
	 * Menu and admin bar hooks are registered.
	 *
	 * @return void
	 */
	public function test_hooks() {
		$this->assertNotFalse( has_action( 'admin_menu' ) );
		$this->assertNotFalse( has_action( 'admin_bar_menu' ) );
	}
}
