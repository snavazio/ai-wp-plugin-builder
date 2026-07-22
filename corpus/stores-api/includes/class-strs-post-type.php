<?php
/**
 * Custom Post Type registration for Stores.
 *
 * @package Strs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Stores custom post type.
 */
class Strs_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_strs_store', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Register the Stores custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Stores', 'Post Type General Name', 'stores-api' ),
			'singular_name'         => _x( 'Store', 'Post Type Singular Name', 'stores-api' ),
			'menu_name'             => __( 'Stores', 'stores-api' ),
			'name_admin_bar'        => __( 'Store', 'stores-api' ),
			'archives'              => __( 'Store Archives', 'stores-api' ),
			'attributes'            => __( 'Store Attributes', 'stores-api' ),
			'parent_item_colon'     => __( 'Parent Store:', 'stores-api' ),
			'all_items'             => __( 'All Stores', 'stores-api' ),
			'add_new_item'          => __( 'Add New Store', 'stores-api' ),
			'add_new'               => __( 'Add New', 'stores-api' ),
			'new_item'              => __( 'New Store', 'stores-api' ),
			'edit_item'             => __( 'Edit Store', 'stores-api' ),
			'update_item'           => __( 'Update Store', 'stores-api' ),
			'view_item'             => __( 'View Store', 'stores-api' ),
			'view_items'            => __( 'View Stores', 'stores-api' ),
			'search_items'          => __( 'Search Store', 'stores-api' ),
			'not_found'             => __( 'Not found', 'stores-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'stores-api' ),
			'featured_image'        => __( 'Featured Image', 'stores-api' ),
			'set_featured_image'    => __( 'Set featured image', 'stores-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'stores-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'stores-api' ),
			'insert_into_item'      => __( 'Insert into store', 'stores-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this store', 'stores-api' ),
			'items_list'            => __( 'Stores list', 'stores-api' ),
			'items_list_navigation' => __( 'Stores list navigation', 'stores-api' ),
			'filter_items_list'     => __( 'Filter stores list', 'stores-api' ),
		);

		$args = array(
			'label'               => __( 'Store', 'stores-api' ),
			'description'         => __( 'Store with address and hours meta', 'stores-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-store',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( 'strs_store', $args );
	}

	/**
	 * Add meta boxes for store address and hours.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'strs_store_address_hours',
			__( 'Address & Hours', 'stores-api' ),
			array( __CLASS__, 'render_meta_box' ),
			'strs_store',
			'normal',
			'default'
		);
	}

	/**
	 * Render the store address and hours meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'strs_save_store_meta', 'strs_store_meta_nonce' );

		$address = get_post_meta( $post->ID, 'strs_address', true );
		$hours   = get_post_meta( $post->ID, 'strs_hours', true );
		?>
		<p>
			<label for="strs_address"><?php esc_html_e( 'Address:', 'stores-api' ); ?></label>
			<br>
			<input type="text" id="strs_address" name="strs_address" value="<?php echo esc_attr( $address ); ?>" class="widefat" />
		</p>
		<p>
			<label for="strs_hours"><?php esc_html_e( 'Hours:', 'stores-api' ); ?></label>
			<br>
			<textarea id="strs_hours" name="strs_hours" class="widefat" rows="5"><?php echo esc_textarea( $hours ); ?></textarea>
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
		if ( ! isset( $_POST['strs_store_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['strs_store_meta_nonce'] ) ), 'strs_save_store_meta' ) ) {
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

		// Save address.
		if ( isset( $_POST['strs_address'] ) ) {
			$address = sanitize_text_field( wp_unslash( $_POST['strs_address'] ) );
			update_post_meta( $post_id, 'strs_address', $address );
		} else {
			delete_post_meta( $post_id, 'strs_address' );
		}

		// Save hours.
		if ( isset( $_POST['strs_hours'] ) ) {
			$hours = sanitize_textarea_field( wp_unslash( $_POST['strs_hours'] ) );
			update_post_meta( $post_id, 'strs_hours', $hours );
		} else {
			delete_post_meta( $post_id, 'strs_hours' );
		}
	}
}
