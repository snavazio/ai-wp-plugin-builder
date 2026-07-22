<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cook
 */

/**
 * Cookbook smoke test.
 */
class Cook_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'COOK_VERSION' ), 'COOK_VERSION should be defined.' );
		$this->assertSame( '1.0.0', COOK_VERSION );
	}

	/**
	 * The recipe post type exists.
	 *
	 * @return void
	 */
	public function test_recipe_post_type_exists() {
		$this->assertTrue( post_type_exists( 'recipe' ), 'recipe post type should exist.' );
	}

	/**
	 * The cook_course taxonomy exists.
	 *
	 * @return void
	 */
	public function test_course_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'cook_course' ), 'cook_course taxonomy should exist.' );
	}

	/**
	 * The cook_cuisine taxonomy exists.
	 *
	 * @return void
	 */
	public function test_cuisine_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'cook_cuisine' ), 'cook_cuisine taxonomy should exist.' );
	}

	/**
	 * The recipes shortcode exists.
	 *
	 * @return void
	 */
	public function test_recipes_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'recipes' ), 'recipes shortcode should exist.' );
	}
}
