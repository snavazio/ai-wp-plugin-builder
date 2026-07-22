<?php
/**
 * Uninstall cleanup for Store Products.
 *
 * @package Strp
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Remove product meta data.
$products = get_posts(
	array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $products as $product_id ) {
	delete_post_meta( $product_id, 'strp_price' );
	delete_post_meta( $product_id, 'strp_sku' );
}
