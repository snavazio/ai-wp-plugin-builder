<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Jbrd
 */

/**
 * Job Board smoke test.
 */
class Jbrd_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'JBRD_VERSION' ), 'JBRD_VERSION should be defined.' );
		$this->assertSame( '1.0.0', JBRD_VERSION );
	}

	/**
	 * The plugin registered the custom post type.
	 *
	 * @return void
	 */
	public function test_post_type_registered() {
		$this->assertTrue( post_type_exists( 'jbrd_job' ), 'Custom post type "jbrd_job" should exist.' );
	}

	/**
	 * The plugin registered the custom taxonomy.
	 *
	 * @return void
	 */
	public function test_taxonomy_registered() {
		$this->assertTrue( taxonomy_exists( 'jbrd_job_category' ), 'Custom taxonomy "jbrd_job_category" should exist.' );
	}

	/**
	 * The plugin registered the shortcode.
	 *
	 * @return void
	 */
	public function test_shortcode_registered() {
		$this->assertTrue( shortcode_exists( 'jobs' ), 'Shortcode "jobs" should exist.' );
	}
}
