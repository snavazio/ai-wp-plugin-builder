<?php
/**
 * Register custom post type for Beers and add ABV meta box.
 *
 * @package Beers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the beer custom post type.
 *
 * @return void
 */
function beers_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Beers', 'Post Type General Name', 'beer-list' ),
		'singular_name'         => _x( 'Beer', 'Post Type Singular Name', 'beer-list' ),
		'menu_name'             => __( 'Beers', 'beer-list' ),
		'name_admin_bar'        => __( 'Beer', 'beer-list' ),
		'archives'              => __( 'Beer Archives', 'beer-list' ),
		'attributes'            => __( 'Beer Attributes', 'beer-list' ),
		'parent_item_colon'     => __( 'Parent Beer:', 'beer-list' ),
		'all_items'             => __( 'All Beers', 'beer-list' ),
		'add_new_item'          => __( 'Add New Beer', 'beer-list' ),
		'add_new'               => __( 'Add New', 'beer-list' ),
		'new_item'              => __( 'New Beer', 'beer-list' ),
		'edit_item'             => __( 'Edit Beer', 'beer-list' ),
		'update_item'           => __( 'Update Beer', 'beer-list' ),
		'view_item'             => __( 'View Beer', 'beer-list' ),
		'view_items'            => __( 'View Beers', 'beer-list' ),
		'search_items'          => __( 'Search Beers', 'beer-list' ),
		'not_found'             => __( 'Not found', 'beer-list' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'beer-list' ),
		'featured_image'        => __( 'Featured Image', 'beer-list' ),
		'set_featured_image'    => __( 'Set featured image', 'beer-list' ),
		'remove_featured_image' => __( 'Remove featured image', 'beer-list' ),
		'use_featured_image'    => __( 'Use as featured image', 'beer-list' ),
		'insert_into_item'      => __( 'Insert into beer', 'beer-list' ),
		'uploaded_to_this_item' => __( 'Uploaded to this beer', 'beer-list' ),
		'items_list'            => __( 'Beers list', 'beer-list' ),
		'items_list_navigation' => __( 'Beers list navigation', 'beer-list' ),
		'filter_items_list'     => __( 'Filter beers list', 'beer-list' ),
	);

	$args = array(
		'label'               => __( 'Beer', 'beer-list' ),
		'description'         => __( 'Beers with ABV meta', 'beer-list' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-beer',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'beer', $args );
}
add_action( 'init', 'beers_register_post_types' );

/**
 * Add ABV meta box for beer posts.
 *
 * @return void
 */
function beers_add_abv_meta_box() {
	add_meta_box(
		'beers_abv_meta_box',
		__( 'ABV', 'beer-list' ),
		'beers_render_abv_meta_box',
		'beer',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'beers_add_abv_meta_box' );

/**
 * Render the ABV meta box.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function beers_render_abv_meta_box( $post ) {
	$abv = get_post_meta( $post->ID, 'beers_abv', true );

	// Nonce for security.
	wp_nonce_field( 'beers_abv_meta_box', 'beers_abv_meta_box_nonce' );
	?>
	<table class="form-table">
		<tr>
			<th><label for="beers_abv"><?php esc_html_e( 'Alcohol by Volume (ABV)', 'beer-list' ); ?></label></th>
			<td>
				<input type="number" id="beers_abv" name="beers_abv" value="<?php echo esc_attr( $abv ); ?>" step="0.1" min="0" max="20" class="regular-text" />
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Save ABV meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function beers_save_abv_meta_box( $post_id, $post ) {
	// Verify nonce.
	if ( ! isset( $_POST['beers_abv_meta_box_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['beers_abv_meta_box_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'beers_abv_meta_box' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$abv = isset( $_POST['beers_abv'] ) ? floatval( wp_unslash( $_POST['beers_abv'] ) ) : 0.0;

	// Update meta.
	update_post_meta( $post_id, 'beers_abv', $abv );
}
add_action( 'save_post', 'beers_save_abv_meta_box', 10, 2 );
