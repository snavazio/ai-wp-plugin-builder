<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Prod
 */

/**
 * Products Catalog smoke test.
 */
class Prod_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PROD_VERSION' ), 'PROD_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PROD_VERSION );
	}

	/**
	 * The product post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'product' ), 'product post type should exist.' );
	}

	/**
	 * The product_category taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'product_category' ), 'product_category taxonomy should exist.' );
	}

	/**
	 * The products shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'products' ), 'products shortcode should exist.' );
	}
}
