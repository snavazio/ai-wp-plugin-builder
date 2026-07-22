<?php
/**
 * Custom Post Type registration for Testimonials.
 *
 * @package Tstm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Testimonials custom post type.
 */
class Tstm_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_tstm_testimonial', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Register the Testimonials custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Testimonials', 'Post Type General Name', 'testimonials-api' ),
			'singular_name'         => _x( 'Testimonial', 'Post Type Singular Name', 'testimonials-api' ),
			'menu_name'             => __( 'Testimonials', 'testimonials-api' ),
			'name_admin_bar'        => __( 'Testimonial', 'testimonials-api' ),
			'archives'              => __( 'Testimonial Archives', 'testimonials-api' ),
			'attributes'            => __( 'Testimonial Attributes', 'testimonials-api' ),
			'parent_item_colon'     => __( 'Parent Testimonial:', 'testimonials-api' ),
			'all_items'             => __( 'All Testimonials', 'testimonials-api' ),
			'add_new_item'          => __( 'Add New Testimonial', 'testimonials-api' ),
			'add_new'               => __( 'Add New', 'testimonials-api' ),
			'new_item'              => __( 'New Testimonial', 'testimonials-api' ),
			'edit_item'             => __( 'Edit Testimonial', 'testimonials-api' ),
			'update_item'           => __( 'Update Testimonial', 'testimonials-api' ),
			'view_item'             => __( 'View Testimonial', 'testimonials-api' ),
			'view_items'            => __( 'View Testimonials', 'testimonials-api' ),
			'search_items'          => __( 'Search Testimonial', 'testimonials-api' ),
			'not_found'             => __( 'Not found', 'testimonials-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'testimonials-api' ),
			'featured_image'        => __( 'Featured Image', 'testimonials-api' ),
			'set_featured_image'    => __( 'Set featured image', 'testimonials-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'testimonials-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'testimonials-api' ),
			'insert_into_item'      => __( 'Insert into testimonial', 'testimonials-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this testimonial', 'testimonials-api' ),
			'items_list'            => __( 'Testimonials list', 'testimonials-api' ),
			'items_list_navigation' => __( 'Testimonials list navigation', 'testimonials-api' ),
			'filter_items_list'     => __( 'Filter testimonials list', 'testimonials-api' ),
		);

		$args = array(
			'label'               => __( 'Testimonial', 'testimonials-api' ),
			'description'         => __( 'Testimonial with author meta', 'testimonials-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-format-quote',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( 'tstm_testimonial', $args );
	}

	/**
	 * Add meta boxes for testimonial author.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'tstm_testimonial_author',
			__( 'Author', 'testimonials-api' ),
			array( __CLASS__, 'render_meta_box' ),
			'tstm_testimonial',
			'normal',
			'default'
		);
	}

	/**
	 * Render the testimonial author meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'tstm_save_testimonial_author', 'tstm_testimonial_author_nonce' );

		$author = get_post_meta( $post->ID, 'tstm_author', true );
		?>
		<p>
			<label for="tstm_author"><?php esc_html_e( 'Author Name:', 'testimonials-api' ); ?></label>
			<br>
			<input type="text" id="tstm_author" name="tstm_author" value="<?php echo esc_attr( $author ); ?>" class="widefat" />
		</p>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object (unused but required by WordPress hook signature).
	 * @return void
	 */
	public static function save_meta_box( $post_id, $post ) {
		// Verify nonce.
		if ( ! isset( $_POST['tstm_testimonial_author_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tstm_testimonial_author_nonce'] ) ), 'tstm_save_testimonial_author' ) ) {
			return;
		}

		// Check user capability.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Avoid autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Save author.
		if ( isset( $_POST['tstm_author'] ) ) {
			$author = sanitize_text_field( wp_unslash( $_POST['tstm_author'] ) );
			update_post_meta( $post_id, 'tstm_author', $author );
		} else {
			delete_post_meta( $post_id, 'tstm_author' );
		}
	}
}
