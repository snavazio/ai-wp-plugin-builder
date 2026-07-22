<?php
/**
 * Register custom post type for Business Listings.
 *
 * @package Bdp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the bdp_listing custom post type.
 *
 * @return void
 */
function bdp_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Listings', 'Post Type General Name', 'business-directory-pro' ),
		'singular_name'         => _x( 'Listing', 'Post Type Singular Name', 'business-directory-pro' ),
		'menu_name'             => __( 'Business Listings', 'business-directory-pro' ),
		'name_admin_bar'        => __( 'Listing', 'business-directory-pro' ),
		'archives'              => __( 'Listing Archives', 'business-directory-pro' ),
		'attributes'            => __( 'Listing Attributes', 'business-directory-pro' ),
		'parent_item_colon'     => __( 'Parent Listing:', 'business-directory-pro' ),
		'all_items'             => __( 'All Listings', 'business-directory-pro' ),
		'add_new_item'          => __( 'Add New Listing', 'business-directory-pro' ),
		'add_new'               => __( 'Add New', 'business-directory-pro' ),
		'new_item'              => __( 'New Listing', 'business-directory-pro' ),
		'edit_item'             => __( 'Edit Listing', 'business-directory-pro' ),
		'update_item'           => __( 'Update Listing', 'business-directory-pro' ),
		'view_item'             => __( 'View Listing', 'business-directory-pro' ),
		'view_items'            => __( 'View Listings', 'business-directory-pro' ),
		'search_items'          => __( 'Search Listings', 'business-directory-pro' ),
		'not_found'             => __( 'Not found', 'business-directory-pro' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'business-directory-pro' ),
		'featured_image'        => __( 'Featured Image', 'business-directory-pro' ),
		'set_featured_image'    => __( 'Set featured image', 'business-directory-pro' ),
		'remove_featured_image' => __( 'Remove featured image', 'business-directory-pro' ),
		'use_featured_image'    => __( 'Use as featured image', 'business-directory-pro' ),
		'insert_into_item'      => __( 'Insert into listing', 'business-directory-pro' ),
		'uploaded_to_this_item' => __( 'Uploaded to this listing', 'business-directory-pro' ),
		'items_list'            => __( 'Listings list', 'business-directory-pro' ),
		'items_list_navigation' => __( 'Listings list navigation', 'business-directory-pro' ),
		'filter_items_list'     => __( 'Filter listings list', 'business-directory-pro' ),
	);

	$args = array(
		'label'               => __( 'Listing', 'business-directory-pro' ),
		'description'         => __( 'Business listings with phone and website', 'business-directory-pro' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-admin-site',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'bdp_listing', $args );
}
add_action( 'init', 'bdp_register_post_types' );

/**
 * Add meta box for phone and website.
 *
 * @return void
 */
function bdp_add_listing_meta_box() {
	add_meta_box(
		'bdp_listing_meta_box',
		__( 'Business Details', 'business-directory-pro' ),
		'bdp_render_listing_meta_box',
		'bdp_listing',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'bdp_add_listing_meta_box' );

/**
 * Render the meta box for phone and website.
 *
 * @param WP_Post $post The post object.
 * @return void
 */
function bdp_render_listing_meta_box( $post ) {
	$phone   = get_post_meta( $post->ID, 'bdp_phone', true );
	$website = get_post_meta( $post->ID, 'bdp_website', true );

	wp_nonce_field( 'bdp_listing_meta_box_nonce', 'bdp_listing_meta_box_nonce' );
	?>
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="bdp_phone"><?php esc_html_e( 'Phone', 'business-directory-pro' ); ?></label>
			</th>
			<td>
				<input type="text" id="bdp_phone" name="bdp_phone" value="<?php echo esc_attr( $phone ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="bdp_website"><?php esc_html_e( 'Website', 'business-directory-pro' ); ?></label>
			</th>
			<td>
				<input type="url" id="bdp_website" name="bdp_website" value="<?php echo esc_url( $website ); ?>" class="regular-text" />
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Save the meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function bdp_save_listing_meta_box( $post_id, $post ) {
	if ( ! isset( $_POST['bdp_listing_meta_box_nonce'] ) ) {
		return;
	}
	$nonce = sanitize_text_field( wp_unslash( $_POST['bdp_listing_meta_box_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'bdp_listing_meta_box_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['bdp_phone'] ) || ! isset( $_POST['bdp_website'] ) ) {
		return;
	}

	$phone   = sanitize_text_field( wp_unslash( $_POST['bdp_phone'] ) );
	$website = esc_url_raw( wp_unslash( $_POST['bdp_website'] ) );

	update_post_meta( $post_id, 'bdp_phone', $phone );
	update_post_meta( $post_id, 'bdp_website', $website );
}
add_action( 'save_post', 'bdp_save_listing_meta_box', 10, 2 );

/**
 * Add Category column to listings list table.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function bdp_add_category_column( $columns ) {
	$columns['bdp_category'] = __( 'Category', 'business-directory-pro' );
	return $columns;
}
add_filter( 'manage_bdp_listing_posts_columns', 'bdp_add_category_column' );

/**
 * Display category in the custom column.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function bdp_display_category_column( $column_name, $post_id ) {
	if ( 'bdp_category' === $column_name ) {
		$terms = get_the_terms( $post_id, 'category' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$term_names = wp_list_pluck( $terms, 'name' );
			echo esc_html( implode( ', ', $term_names ) );
		} else {
			echo esc_html__( 'No category', 'business-directory-pro' );
		}
	}
}
add_action( 'manage_bdp_listing_posts_custom_column', 'bdp_display_category_column', 10, 2 );
