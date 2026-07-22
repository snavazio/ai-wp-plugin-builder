<?php
/**
 * Uninstall cleanup for Restaurant Menu.
 *
 * @package Rmenu
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all menu item posts.
$menu_items = get_posts(
	array(
		'post_type'      => 'menu_item',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $menu_items as $menu_item_id ) {
	wp_delete_post( $menu_item_id, true );
}
