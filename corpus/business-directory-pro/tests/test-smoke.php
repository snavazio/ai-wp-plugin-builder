<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Bdp
 */

/**
 * Business Directory Pro smoke test.
 */
class Bdp_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'BDP_VERSION' ), 'BDP_VERSION should be defined.' );
		$this->assertSame( '1.0.0', BDP_VERSION );
	}

	/**
	 * The plugin registered the listing post type.
	 *
	 * @return void
	 */
	public function test_post_type_registered() {
		$this->assertTrue( post_type_exists( 'bdp_listing' ), 'Post type "bdp_listing" should exist.' );
	}

	/**
	 * The plugin registered the category taxonomy.
	 *
	 * @return void
	 */
	public function test_category_taxonomy_registered() {
		$this->assertTrue( taxonomy_exists( 'category' ), 'Taxonomy "category" should exist.' );
	}

	/**
	 * The plugin registered the region taxonomy.
	 *
	 * @return void
	 */
	public function test_region_taxonomy_registered() {
		$this->assertTrue( taxonomy_exists( 'region' ), 'Taxonomy "region" should exist.' );
	}

	/**
	 * The plugin registered the listings shortcode.
	 *
	 * @return void
	 */
	public function test_shortcode_registered() {
		$this->assertTrue( shortcode_exists( 'listings' ), 'Shortcode "listings" should exist.' );
	}
}
