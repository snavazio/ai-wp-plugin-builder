<?php
/**
 * Register custom post type for Wines and add vintage meta box.
 *
 * @package Winec
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the wine custom post type.
 *
 * @return void
 */
function winec_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Wines', 'Post Type General Name', 'wine-catalog' ),
		'singular_name'         => _x( 'Wine', 'Post Type Singular Name', 'wine-catalog' ),
		'menu_name'             => __( 'Wines', 'wine-catalog' ),
		'name_admin_bar'        => __( 'Wine', 'wine-catalog' ),
		'archives'              => __( 'Wine Archives', 'wine-catalog' ),
		'attributes'            => __( 'Wine Attributes', 'wine-catalog' ),
		'parent_item_colon'     => __( 'Parent Wine:', 'wine-catalog' ),
		'all_items'             => __( 'All Wines', 'wine-catalog' ),
		'add_new_item'          => __( 'Add New Wine', 'wine-catalog' ),
		'add_new'               => __( 'Add New', 'wine-catalog' ),
		'new_item'              => __( 'New Wine', 'wine-catalog' ),
		'edit_item'             => __( 'Edit Wine', 'wine-catalog' ),
		'update_item'           => __( 'Update Wine', 'wine-catalog' ),
		'view_item'             => __( 'View Wine', 'wine-catalog' ),
		'view_items'            => __( 'View Wines', 'wine-catalog' ),
		'search_items'          => __( 'Search Wines', 'wine-catalog' ),
		'not_found'             => __( 'Not found', 'wine-catalog' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'wine-catalog' ),
		'featured_image'        => __( 'Featured Image', 'wine-catalog' ),
		'set_featured_image'    => __( 'Set featured image', 'wine-catalog' ),
		'remove_featured_image' => __( 'Remove featured image', 'wine-catalog' ),
		'use_featured_image'    => __( 'Use as featured image', 'wine-catalog' ),
		'insert_into_item'      => __( 'Insert into wine', 'wine-catalog' ),
		'uploaded_to_this_item' => __( 'Uploaded to this wine', 'wine-catalog' ),
		'items_list'            => __( 'Wines list', 'wine-catalog' ),
		'items_list_navigation' => __( 'Wines list navigation', 'wine-catalog' ),
		'filter_items_list'     => __( 'Filter wines list', 'wine-catalog' ),
	);

	$args = array(
		'label'               => __( 'Wine', 'wine-catalog' ),
		'description'         => __( 'Wines with vintage meta', 'wine-catalog' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-wine-glass',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'wine', $args );
}
add_action( 'init', 'winec_register_post_types' );

/**
 * Add vintage meta box for wine posts.
 *
 * @return void
 */
function winec_add_vintage_meta_box() {
	add_meta_box(
		'winec_vintage_meta_box',
		__( 'Vintage', 'wine-catalog' ),
		'winec_render_vintage_meta_box',
		'wine',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'winec_add_vintage_meta_box' );

/**
 * Render the vintage meta box.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function winec_render_vintage_meta_box( $post ) {
	$vintage = get_post_meta( $post->ID, 'winec_vintage', true );

	// Nonce for security.
	wp_nonce_field( 'winec_vintage_meta_box', 'winec_vintage_meta_box_nonce' );
	?>
	<table class="form-table">
		<tr>
			<th><label for="winec_vintage"><?php esc_html_e( 'Vintage Year', 'wine-catalog' ); ?></label></th>
			<td>
				<input type="number" id="winec_vintage" name="winec_vintage" value="<?php echo esc_attr( $vintage ); ?>" min="1800" max="2100" class="regular-text" />
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Save vintage meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function winec_save_vintage_meta_box( $post_id, $post ) {
	// Verify nonce.
	if ( ! isset( $_POST['winec_vintage_meta_box_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['winec_vintage_meta_box_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'winec_vintage_meta_box' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$vintage = isset( $_POST['winec_vintage'] ) ? absint( wp_unslash( $_POST['winec_vintage'] ) ) : 0;

	// Update meta.
	update_post_meta( $post_id, 'winec_vintage', $vintage );
}
add_action( 'save_post', 'winec_save_vintage_meta_box', 10, 2 );
