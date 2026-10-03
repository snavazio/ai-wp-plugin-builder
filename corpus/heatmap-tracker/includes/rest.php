<?php
/**
 * REST routes: public event collection and admin-only results.
 *
 * @package Hmtrk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register REST routes.
 *
 * @return void
 */
function hmtrk_register_routes() {
	register_rest_route(
		'hmtrk/v1',
		'/events',
		array(
			'methods'             => 'POST',
			'callback'            => 'hmtrk_rest_collect',
			// Reviewed exception: anonymous visitors must send events (page caching breaks anonymous nonces); abuse bounded by per-IP throttle.
			'permission_callback' => 'hmtrk_can_collect',
			'args'                => array(
				'post_id'      => array(
					'required'          => true,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'hmtrk_validate_post_id',
				),
				'event_type'   => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => 'hmtrk_validate_event_type',
				),
				'device'       => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => 'hmtrk_validate_device',
				),
				'x'            => array(
					'default'           => 0,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'hmtrk_validate_x',
				),
				'y'            => array(
					'default'           => 0,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'hmtrk_validate_y',
				),
				'scroll_depth' => array(
					'default'           => 0,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'hmtrk_validate_depth',
				),
			),
		)
	);

	register_rest_route(
		'hmtrk/v1',
		'/results',
		array(
			'methods'             => 'GET',
			'callback'            => 'hmtrk_rest_results',
			'permission_callback' => 'hmtrk_can_manage',
			'args'                => array(
				'post_id' => array(
					'required'          => true,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'hmtrk_validate_post_id',
				),
				'device'  => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => 'hmtrk_validate_device',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'hmtrk_register_routes' );

/**
 * Permission callback for admin-only routes.
 *
 * @return bool
 */
function hmtrk_can_manage() {
	return current_user_can( 'manage_options' );
}

/**
 * Permission callback for the public collector: per-IP throttle.
 *
 * Allows at most 60 events (filterable) per minute from one address.
 *
 * @return true|WP_Error
 */
function hmtrk_can_collect() {
	$limit = (int) apply_filters( 'hmtrk_rate_limit', 60 );
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'hmtrk_rl_' . md5( $ip );
	$count = (int) get_transient( $key );

	if ( $count >= $limit ) {
		return new WP_Error( 'hmtrk_rate_limited', __( 'Too many requests.', 'heatmap-tracker' ), array( 'status' => 429 ) );
	}

	set_transient( $key, $count + 1, MINUTE_IN_SECONDS );
	return true;
}

/**
 * Validate a numeric parameter within bounds.
 *
 * @param mixed $value Value.
 * @param int   $max   Maximum.
 * @return bool
 */
function hmtrk_validate_range( $value, $max ) {
	return is_numeric( $value ) && $value >= 0 && $value <= $max;
}

/**
 * Validate post_id.
 *
 * @param mixed $value Value.
 * @return bool
 */
function hmtrk_validate_post_id( $value ) {
	return is_numeric( $value ) && $value > 0 && $value < 4294967295;
}

/**
 * Validate event type.
 *
 * @param mixed $value Value.
 * @return bool
 */
function hmtrk_validate_event_type( $value ) {
	return is_string( $value ) && in_array( $value, array( 'click', 'scroll' ), true );
}

/**
 * Validate device.
 *
 * @param mixed $value Value.
 * @return bool
 */
function hmtrk_validate_device( $value ) {
	return is_string( $value ) && in_array( $value, array( 'desktop', 'tablet', 'mobile' ), true );
}

/**
 * Validate x position (tenths of a percent of page width).
 *
 * @param mixed $value Value.
 * @return bool
 */
function hmtrk_validate_x( $value ) {
	return hmtrk_validate_range( $value, 1000 );
}

/**
 * Validate y position (pixels from page top).
 *
 * @param mixed $value Value.
 * @return bool
 */
function hmtrk_validate_y( $value ) {
	return hmtrk_validate_range( $value, 100000 );
}

/**
 * Validate scroll depth (percent).
 *
 * @param mixed $value Value.
 * @return bool
 */
function hmtrk_validate_depth( $value ) {
	return hmtrk_validate_range( $value, 100 );
}

/**
 * Store one event.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hmtrk_rest_collect( $request ) {
	if ( strlen( (string) $request->get_body() ) > 1024 ) {
		return new WP_Error( 'hmtrk_too_large', __( 'Payload too large.', 'heatmap-tracker' ), array( 'status' => 413 ) );
	}
	if ( current_user_can( 'manage_options' ) ) {
		return new WP_REST_Response( null, 204 );
	}

	$post_id = absint( $request->get_param( 'post_id' ) );
	if ( ! hmtrk_is_tracked( $post_id ) ) {
		return new WP_Error( 'hmtrk_untracked', __( 'Not tracked.', 'heatmap-tracker' ), array( 'status' => 400 ) );
	}

	$type = (string) $request->get_param( 'event_type' );

	global $wpdb;
	$ok = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		hmtrk_table(),
		array(
			'post_id'      => $post_id,
			'event_type'   => $type,
			'pos_x'        => 'click' === $type ? absint( $request->get_param( 'x' ) ) : 0,
			'pos_y'        => 'click' === $type ? absint( $request->get_param( 'y' ) ) : 0,
			'scroll_depth' => 'scroll' === $type ? absint( $request->get_param( 'scroll_depth' ) ) : 0,
			'device'       => (string) $request->get_param( 'device' ),
			'created_at'   => current_time( 'mysql', true ),
		),
		array( '%d', '%s', '%d', '%d', '%d', '%s', '%s' )
	);

	return new WP_REST_Response( null, $ok ? 204 : 500 );
}

/**
 * Aggregate results for the admin overlay.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function hmtrk_rest_results( $request ) {
	global $wpdb;
	$table   = hmtrk_table();
	$post_id = absint( $request->get_param( 'post_id' ) );
	$device  = (string) $request->get_param( 'device' );

	// Table name comes from $wpdb->prefix; values are bound with placeholders.
	// phpcs:disable WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$clicks = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ROUND(pos_x / 10) AS gx, FLOOR(pos_y / 20) * 20 AS gy, COUNT(*) AS c FROM {$table} WHERE post_id = %d AND device = %s AND event_type = %s GROUP BY gx, gy ORDER BY c DESC LIMIT 2000",
			$post_id,
			$device,
			'click'
		)
	);

	$depths = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT scroll_depth AS d, COUNT(*) AS c FROM {$table} WHERE post_id = %d AND device = %s AND event_type = %s GROUP BY scroll_depth",
			$post_id,
			$device,
			'scroll'
		)
	);

	// phpcs:enable WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	$points = array();
	foreach ( (array) $clicks as $row ) {
		$points[] = array(
			'x' => (int) $row->gx,
			'y' => (int) $row->gy,
			'c' => (int) $row->c,
		);
	}

	$total   = 0;
	$buckets = array_fill( 1, 10, 0 );
	foreach ( (array) $depths as $row ) {
		$total += (int) $row->c;
		for ( $b = 1; $b <= 10; $b++ ) {
			if ( (int) $row->d >= $b * 10 ) {
				$buckets[ $b ] += (int) $row->c;
			}
		}
	}

	return rest_ensure_response(
		array(
			'clicks'       => $points,
			'scroll_total' => $total,
			'scroll'       => array_values( $buckets ),
		)
	);
}
