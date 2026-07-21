<?php
/**
 * Smoke test: assert the plugin booted and registered its CPT, shortcode, and admin column hook.
 *
 * @package Good_Plugin
 */

/**
 * Client Notes smoke test.
 */
class Cnote_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The Client Notes CPT should be registered on init.
	 *
	 * @return void
	 */
	public function test_post_type_registered() {
		$this->assertTrue( post_type_exists( 'cnote_note' ), 'CPT cnote_note should be registered.' );
	}

	/**
	 * The shortcode should be registered.
	 *
	 * @return void
	 */
	public function test_shortcode_registered() {
		$this->assertTrue( shortcode_exists( 'client_notes' ), 'Shortcode [client_notes] should exist.' );
	}

	/**
	 * The plugin version constant should be defined and match the header.
	 *
	 * @return void
	 */
	public function test_version_constant_defined() {
		$this->assertTrue( defined( 'CNOTE_VERSION' ), 'CNOTE_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CNOTE_VERSION );
	}

	/**
	 * The admin rating column hook should be wired so the column appears.
	 *
	 * @return void
	 */
	public function test_rating_column_filter_registered() {
		$this->assertNotFalse(
			has_filter( 'manage_cnote_note_posts_columns' ),
			'Rating column filter should be registered.'
		);
	}

	/**
	 * The shortcode renders a graceful empty state when there are no notes.
	 *
	 * @return void
	 */
	public function test_shortcode_empty_state() {
		$output = do_shortcode( '[client_notes]' );
		$this->assertStringContainsString( 'No client notes yet', $output );
	}
}
