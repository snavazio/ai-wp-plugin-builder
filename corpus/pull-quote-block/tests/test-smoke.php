<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Pqbl
 */

/**
 * Pull Quote Block smoke test.
 */
class Pqbl_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PQBL_VERSION' ), 'PQBL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PQBL_VERSION );
	}

	/**
	 * The Pull Quote Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'pull-quote-block/pull-quote' ), 'Pull Quote Block should be registered.' );
	}
}
