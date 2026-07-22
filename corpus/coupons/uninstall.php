<?php
/**
 * Uninstall cleanup for Coupons.
 *
 * @package Cpns
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all coupon posts.
$coupons = get_posts(
	array(
		'post_type'      => 'coupon',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $coupons as $coupon_id ) {
	wp_delete_post( $coupon_id, true );
}
