<?php
/**
 * Custom Post Type registration for Services.
 *
 * @package Srvapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Services custom post type.
 */
class Srvapi_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_srvapi_service', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Register the Services custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Services', 'Post Type General Name', 'services-api' ),
			'singular_name'         => _x( 'Service', 'Post Type Singular Name', 'services-api' ),
			'menu_name'             => __( 'Services', 'services-api' ),
			'name_admin_bar'        => __( 'Service', 'services-api' ),
			'archives'              => __( 'Service Archives', 'services-api' ),
			'attributes'            => __( 'Service Attributes', 'services-api' ),
			'parent_item_colon'     => __( 'Parent Service:', 'services-api' ),
			'all_items'             => __( 'All Services', 'services-api' ),
			'add_new_item'          => __( 'Add New Service', 'services-api' ),
			'add_new'               => __( 'Add New', 'services-api' ),
			'new_item'              => __( 'New Service', 'services-api' ),
			'edit_item'             => __( 'Edit Service', 'services-api' ),
			'update_item'           => __( 'Update Service', 'services-api' ),
			'view_item'             => __( 'View Service', 'services-api' ),
			'view_items'            => __( 'View Services', 'services-api' ),
			'search_items'          => __( 'Search Service', 'services-api' ),
			'not_found'             => __( 'Not found', 'services-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'services-api' ),
			'featured_image'        => __( 'Featured Image', 'services-api' ),
			'set_featured_image'    => __( 'Set featured image', 'services-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'services-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'services-api' ),
			'insert_into_item'      => __( 'Insert into service', 'services-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this service', 'services-api' ),
			'items_list'            => __( 'Services list', 'services-api' ),
			'items_list_navigation' => __( 'Services list navigation', 'services-api' ),
			'filter_items_list'     => __( 'Filter services list', 'services-api' ),
		);

		$args = array(
			'label'               => __( 'Service', 'services-api' ),
			'description'         => __( 'Services with price meta', 'services-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-money-alt',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rest_base'           => 'services',
			'show_in_rest'        => true,
		);

		register_post_type( 'srvapi_service', $args );
	}

	/**
	 * Add meta boxes for service price.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'srvapi_service_price',
			__( 'Service Price', 'services-api' ),
			array( __CLASS__, 'render_meta_box' ),
			'srvapi_service',
			'normal',
			'default'
		);
	}

	/**
	 * Render the service price meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'srvapi_save_service_price', 'srvapi_service_price_nonce' );

		$price = get_post_meta( $post->ID, 'srvapi_price', true );
		?>
		<p>
			<label for="srvapi_price"><?php esc_html_e( 'Price:', 'services-api' ); ?></label>
			<br>
			<input type="text" id="srvapi_price" name="srvapi_price" value="<?php echo esc_attr( $price ); ?>" class="widefat" />
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
		if ( ! isset( $_POST['srvapi_service_price_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['srvapi_service_price_nonce'] ) ), 'srvapi_save_service_price' ) ) {
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

		// Save price.
		if ( isset( $_POST['srvapi_price'] ) ) {
			$price = sanitize_text_field( wp_unslash( $_POST['srvapi_price'] ) );
			update_post_meta( $post_id, 'srvapi_price', $price );
		} else {
			delete_post_meta( $post_id, 'srvapi_price' );
		}
	}
}
