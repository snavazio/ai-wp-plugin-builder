<?php
/**
 * Register custom post type for Jobs.
 *
 * @package Jbrd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the jbrd_job custom post type.
 *
 * @return void
 */
function jbrd_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Jobs', 'Post Type General Name', 'job-board' ),
		'singular_name'         => _x( 'Job', 'Post Type Singular Name', 'job-board' ),
		'menu_name'             => __( 'Jobs', 'job-board' ),
		'name_admin_bar'        => __( 'Job', 'job-board' ),
		'archives'              => __( 'Job Archives', 'job-board' ),
		'attributes'            => __( 'Job Attributes', 'job-board' ),
		'parent_item_colon'     => __( 'Parent Job:', 'job-board' ),
		'all_items'             => __( 'All Jobs', 'job-board' ),
		'add_new_item'          => __( 'Add New Job', 'job-board' ),
		'add_new'               => __( 'Add New', 'job-board' ),
		'new_item'              => __( 'New Job', 'job-board' ),
		'edit_item'             => __( 'Edit Job', 'job-board' ),
		'update_item'           => __( 'Update Job', 'job-board' ),
		'view_item'             => __( 'View Job', 'job-board' ),
		'view_items'            => __( 'View Jobs', 'job-board' ),
		'search_items'          => __( 'Search Jobs', 'job-board' ),
		'not_found'             => __( 'Not found', 'job-board' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'job-board' ),
		'featured_image'        => __( 'Featured Image', 'job-board' ),
		'set_featured_image'    => __( 'Set featured image', 'job-board' ),
		'remove_featured_image' => __( 'Remove featured image', 'job-board' ),
		'use_featured_image'    => __( 'Use as featured image', 'job-board' ),
		'insert_into_item'      => __( 'Insert into job', 'job-board' ),
		'uploaded_to_this_item' => __( 'Uploaded to this job', 'job-board' ),
		'items_list'            => __( 'Jobs list', 'job-board' ),
		'items_list_navigation' => __( 'Jobs list navigation', 'job-board' ),
		'filter_items_list'     => __( 'Filter jobs list', 'job-board' ),
	);

	$args = array(
		'label'               => __( 'Job', 'job-board' ),
		'description'         => __( 'Job listings with location and salary', 'job-board' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-buddicons-microphone',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'jbrd_job', $args );
}
add_action( 'init', 'jbrd_register_post_types' );

/**
 * Add meta box for location and salary.
 *
 * @return void
 */
function jbrd_add_job_meta_box() {
	add_meta_box(
		'jbrd_job_meta_box',
		__( 'Job Details', 'job-board' ),
		'jbrd_render_job_meta_box',
		'jbrd_job',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'jbrd_add_job_meta_box' );

/**
 * Render the meta box for location and salary.
 *
 * @param WP_Post $post The post object.
 * @return void
 */
function jbrd_render_job_meta_box( $post ) {
	$location = get_post_meta( $post->ID, 'jbrd_location', true );
	$salary   = get_post_meta( $post->ID, 'jbrd_salary', true );

	wp_nonce_field( 'jbrd_job_meta_box_nonce', 'jbrd_job_meta_box_nonce' );
	?>
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="jbrd_location"><?php esc_html_e( 'Location', 'job-board' ); ?></label>
			</th>
			<td>
				<input type="text" id="jbrd_location" name="jbrd_location" value="<?php echo esc_attr( $location ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="jbrd_salary"><?php esc_html_e( 'Salary', 'job-board' ); ?></label>
			</th>
			<td>
				<input type="text" id="jbrd_salary" name="jbrd_salary" value="<?php echo esc_attr( $salary ); ?>" class="regular-text" />
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
function jbrd_save_job_meta_box( $post_id, $post ) {
	if ( ! isset( $_POST['jbrd_job_meta_box_nonce'] ) ) {
		return;
	}
	$nonce = sanitize_text_field( wp_unslash( $_POST['jbrd_job_meta_box_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'jbrd_job_meta_box_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['jbrd_location'] ) || ! isset( $_POST['jbrd_salary'] ) ) {
		return;
	}

	$location = sanitize_text_field( wp_unslash( $_POST['jbrd_location'] ) );
	$salary   = sanitize_text_field( wp_unslash( $_POST['jbrd_salary'] ) );

	update_post_meta( $post_id, 'jbrd_location', $location );
	update_post_meta( $post_id, 'jbrd_salary', $salary );
}
add_action( 'save_post', 'jbrd_save_job_meta_box', 10, 2 );

/**
 * Add Job Category column to jobs list table.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function jbrd_add_job_category_column( $columns ) {
	$columns['jbrd_job_category'] = __( 'Category', 'job-board' );
	return $columns;
}
add_filter( 'manage_jbrd_job_posts_columns', 'jbrd_add_job_category_column' );

/**
 * Display category in the custom column.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function jbrd_display_job_category_column( $column_name, $post_id ) {
	if ( 'jbrd_job_category' === $column_name ) {
		$terms = get_the_terms( $post_id, 'jbrd_job_category' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$term_names = wp_list_pluck( $terms, 'name' );
			echo esc_html( implode( ', ', $term_names ) );
		} else {
			echo esc_html__( 'No category', 'job-board' );
		}
	}
}
add_action( 'manage_jbrd_job_posts_custom_column', 'jbrd_display_job_category_column', 10, 2 );
