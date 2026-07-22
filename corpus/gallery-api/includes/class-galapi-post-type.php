<?php
/**
 * Custom Post Type registration for Photos.
 *
 * @package Galapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Photos custom post type.
 */
class Galapi_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_galapi_photo', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Register the Photos custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Photos', 'Post Type General Name', 'gallery-api' ),
			'singular_name'         => _x( 'Photo', 'Post Type Singular Name', 'gallery-api' ),
			'menu_name'             => __( 'Photos', 'gallery-api' ),
			'name_admin_bar'        => __( 'Photo', 'gallery-api' ),
			'archives'              => __( 'Photo Archives', 'gallery-api' ),
			'attributes'            => __( 'Photo Attributes', 'gallery-api' ),
			'parent_item_colon'     => __( 'Parent Photo:', 'gallery-api' ),
			'all_items'             => __( 'All Photos', 'gallery-api' ),
			'add_new_item'          => __( 'Add New Photo', 'gallery-api' ),
			'add_new'               => __( 'Add New', 'gallery-api' ),
			'new_item'              => __( 'New Photo', 'gallery-api' ),
			'edit_item'             => __( 'Edit Photo', 'gallery-api' ),
			'update_item'           => __( 'Update Photo', 'gallery-api' ),
			'view_item'             => __( 'View Photo', 'gallery-api' ),
			'view_items'            => __( 'View Photos', 'gallery-api' ),
			'search_items'          => __( 'Search Photo', 'gallery-api' ),
			'not_found'             => __( 'Not found', 'gallery-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'gallery-api' ),
			'featured_image'        => __( 'Featured Image', 'gallery-api' ),
			'set_featured_image'    => __( 'Set featured image', 'gallery-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'gallery-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'gallery-api' ),
			'insert_into_item'      => __( 'Insert into photo', 'gallery-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this photo', 'gallery-api' ),
			'items_list'            => __( 'Photos list', 'gallery-api' ),
			'items_list_navigation' => __( 'Photos list navigation', 'gallery-api' ),
			'filter_items_list'     => __( 'Filter photos list', 'gallery-api' ),
		);

		$args = array(
			'label'               => __( 'Photo', 'gallery-api' ),
			'description'         => __( 'Published photos with caption meta', 'gallery-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-format-image',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( 'galapi_photo', $args );
	}

	/**
	 * Add meta boxes for photo caption.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'galapi_photo_caption',
			__( 'Caption', 'gallery-api' ),
			array( __CLASS__, 'render_meta_box' ),
			'galapi_photo',
			'normal',
			'default'
		);
	}

	/**
	 * Render the photo caption meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'galapi_save_photo_caption', 'galapi_photo_caption_nonce' );

		$caption = get_post_meta( $post->ID, 'galapi_caption', true );
		?>
		<p>
			<label for="galapi_caption"><?php esc_html_e( 'Caption:', 'gallery-api' ); ?></label>
			<br>
			<textarea id="galapi_caption" name="galapi_caption" class="widefat" rows="3"><?php echo esc_textarea( $caption ); ?></textarea>
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
		if ( ! isset( $_POST['galapi_photo_caption_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['galapi_photo_caption_nonce'] ) ), 'galapi_save_photo_caption' ) ) {
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

		// Save caption.
		if ( isset( $_POST['galapi_caption'] ) ) {
			$caption = sanitize_textarea_field( wp_unslash( $_POST['galapi_caption'] ) );
			update_post_meta( $post_id, 'galapi_caption', $caption );
		} else {
			delete_post_meta( $post_id, 'galapi_caption' );
		}
	}
}
