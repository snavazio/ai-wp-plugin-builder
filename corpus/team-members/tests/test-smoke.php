<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Team
 */

/**
 * Team Members smoke test.
 */
class Team_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TEAM_VERSION' ), 'TEAM_VERSION should be defined.' );
		$this->assertSame( '1.0.0', TEAM_VERSION );
	}

	/**
	 * The team_member post type exists.
	 *
	 * @return void
	 */
	public function test_team_member_post_type_exists() {
		$this->assertTrue( post_type_exists( 'team_member' ), 'Team Member post type should exist.' );
	}
}
