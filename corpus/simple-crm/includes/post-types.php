<?php
/**
 * Register custom post type for Contacts.
 *
 * @package Scrm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the scrm_contact custom post type.
 *
 * @return void
 */
function scrm_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Contacts', 'Post Type General Name', 'simple-crm' ),
		'singular_name'         => _x( 'Contact', 'Post Type Singular Name', 'simple-crm' ),
		'menu_name'             => __( 'Contacts', 'simple-crm' ),
		'name_admin_bar'        => __( 'Contact', 'simple-crm' ),
		'archives'              => __( 'Contact Archives', 'simple-crm' ),
		'attributes'            => __( 'Contact Attributes', 'simple-crm' ),
		'parent_item_colon'     => __( 'Parent Contact:', 'simple-crm' ),
		'all_items'             => __( 'All Contacts', 'simple-crm' ),
		'add_new_item'          => __( 'Add New Contact', 'simple-crm' ),
		'add_new'               => __( 'Add New', 'simple-crm' ),
		'new_item'              => __( 'New Contact', 'simple-crm' ),
		'edit_item'             => __( 'Edit Contact', 'simple-crm' ),
		'update_item'           => __( 'Update Contact', 'simple-crm' ),
		'view_item'             => __( 'View Contact', 'simple-crm' ),
		'view_items'            => __( 'View Contacts', 'simple-crm' ),
		'search_items'          => __( 'Search Contacts', 'simple-crm' ),
		'not_found'             => __( 'Not found', 'simple-crm' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'simple-crm' ),
		'featured_image'        => __( 'Featured Image', 'simple-crm' ),
		'set_featured_image'    => __( 'Set featured image', 'simple-crm' ),
		'remove_featured_image' => __( 'Remove featured image', 'simple-crm' ),
		'use_featured_image'    => __( 'Use as featured image', 'simple-crm' ),
		'insert_into_item'      => __( 'Insert into contact', 'simple-crm' ),
		'uploaded_to_this_item' => __( 'Uploaded to this contact', 'simple-crm' ),
		'items_list'            => __( 'Contacts list', 'simple-crm' ),
		'items_list_navigation' => __( 'Contacts list navigation', 'simple-crm' ),
		'filter_items_list'     => __( 'Filter contacts list', 'simple-crm' ),
	);

	$args = array(
		'label'               => __( 'Contact', 'simple-crm' ),
		'description'         => __( 'Contacts with email and phone meta', 'simple-crm' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-id',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'scrm_contact', $args );
}
add_action( 'init', 'scrm_register_post_types' );

/**
 * Add meta box for contact email and phone.
 *
 * @return void
 */
function scrm_add_contact_meta_box() {
	add_meta_box(
		'scrm_contact_meta_box',
		__( 'Contact Details', 'simple-crm' ),
		'scrm_render_contact_meta_box',
		'scrm_contact',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'scrm_add_contact_meta_box' );

/**
 * Render the contact meta box.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function scrm_render_contact_meta_box( $post ) {
	$email = get_post_meta( $post->ID, 'scrm_contact_email', true );
	$phone = get_post_meta( $post->ID, 'scrm_contact_phone', true );

	// Nonce for security.
	wp_nonce_field( 'scrm_contact_meta_box', 'scrm_contact_meta_box_nonce' );
	?>
	<table class="form-table">
		<tr>
			<th><label for="scrm_contact_email"><?php esc_html_e( 'Email', 'simple-crm' ); ?></label></th>
			<td>
				<input type="email" id="scrm_contact_email" name="scrm_contact_email" value="<?php echo esc_attr( $email ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th><label for="scrm_contact_phone"><?php esc_html_e( 'Phone', 'simple-crm' ); ?></label></th>
			<td>
				<input type="tel" id="scrm_contact_phone" name="scrm_contact_phone" value="<?php echo esc_attr( $phone ); ?>" class="regular-text" />
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Save contact meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function scrm_save_contact_meta_box( $post_id, $post ) {
	// Verify nonce.
	if ( ! isset( $_POST['scrm_contact_meta_box_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['scrm_contact_meta_box_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'scrm_contact_meta_box' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$email = isset( $_POST['scrm_contact_email'] ) ? sanitize_email( wp_unslash( $_POST['scrm_contact_email'] ) ) : '';
	$phone = isset( $_POST['scrm_contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['scrm_contact_phone'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'scrm_contact_email', $email );
	update_post_meta( $post_id, 'scrm_contact_phone', $phone );
}
add_action( 'save_post', 'scrm_save_contact_meta_box', 10, 2 );

/**
 * Add status column to contacts list.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function scrm_add_contact_status_column( $columns ) {
	$columns['scrm_status'] = __( 'Status', 'simple-crm' );
	return $columns;
}
add_filter( 'manage_scrm_contact_posts_columns', 'scrm_add_contact_status_column' );

/**
 * Display status in the contact list column.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function scrm_display_contact_status_column( $column_name, $post_id ) {
	if ( 'scrm_status' === $column_name ) {
		$statuses = get_the_terms( $post_id, 'scrm_status' );
		if ( $statuses && ! is_wp_error( $statuses ) ) {
			$status_names = array();
			foreach ( $statuses as $status ) {
				$status_names[] = esc_html( $status->name );
			}
			echo esc_html( implode( ', ', $status_names ) );
		} else {
			echo esc_html__( 'None', 'simple-crm' );
		}
	}
}
add_action( 'manage_scrm_contact_posts_custom_column', 'scrm_display_contact_status_column', 10, 2 );
