<?php
/**
 * Storage functions for Google YouTube Transcript Importer.
 *
 * @package Gyti
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set Google OAuth token.
 *
 * @param string $token Google OAuth token.
 * @return void
 */
function gyti_set_google_token( $token ) {
	$token = sanitize_text_field( $token );
	update_option( 'gyti_google_token', $token );
}

/**
 * Get Google OAuth token.
 *
 * @return string Google OAuth token.
 */
function gyti_get_google_token() {
	return get_option( 'gyti_google_token', '' );
}

/**
 * Store YouTube video data as post meta.
 *
 * @param int   $post_id Post ID.
 * @param array $video_data Video data array.
 * @return void
 */
function gyti_store_video_data( $post_id, $video_data ) {
	$video_id   = sanitize_text_field( $video_data['video_id'] );
	$transcript = wp_kses_post( $video_data['transcript'] );

	update_post_meta(
		$post_id,
		'_gyti_video_data',
		array(
			'video_id'   => $video_id,
			'transcript' => $transcript,
		)
	);
}
