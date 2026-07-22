<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Tdir
 */

/**
 * Team Directory smoke test.
 */
class Tdir_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TDIR_VERSION' ), 'TDIR_VERSION should be defined.' );
		$this->assertSame( '1.0.0', TDIR_VERSION );
	}

	/**
	 * The team member post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'tdir_team_member' ), 'tdir_team_member post type should exist.' );
	}

	/**
	 * The department taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'tdir_department' ), 'tdir_department taxonomy should exist.' );
	}

	/**
	 * The tdir_team shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'tdir_team' ), 'tdir_team shortcode should exist.' );
	}
}
