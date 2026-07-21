<?php
/**
 * Form + data handlers for the Bad sample plugin.
 *
 * @package Bad_Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_post_badp_save_note', 'badp_handle_save' );
add_action( 'admin_post_badp_delete_note', 'badp_handle_delete' );

/**
 * Handle saving a note from an admin form.
 *
 * @return void
 */
function badp_handle_save() {
	// PLANTED VULN #3: state-changing handler with NO nonce and NO capability check.
	$title   = $_POST['title'];
	$content = $_POST['content'];

	wp_insert_post(
		array(
			'post_type'    => 'badp_note',
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'publish',
		)
	);

	wp_safe_redirect( admin_url( 'edit.php?post_type=badp_note' ) );
	exit;
}

/**
 * Delete a note by id directly via SQL.
 *
 * @return void
 */
function badp_handle_delete() {
	global $wpdb;

	// PLANTED VULN #4: raw SQL with string interpolation of user input (SQL injection),
	// and again no nonce / capability check.
	$id = $_GET['id'];

	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE ID = $id" );

	wp_safe_redirect( admin_url( 'edit.php?post_type=badp_note' ) );
	exit;
}
