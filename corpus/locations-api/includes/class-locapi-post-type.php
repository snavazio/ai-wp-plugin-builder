<?php
/**
 * Custom Post Type registration for Locations.
 *
 * @package Locapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Locations custom post type.
 */
class Locapi_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_locapi_location', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Register the Locations custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Locations', 'Post Type General Name', 'locations-api' ),
			'singular_name'         => _x( 'Location', 'Post Type Singular Name', 'locations-api' ),
			'menu_name'             => __( 'Locations', 'locations-api' ),
			'name_admin_bar'        => __( 'Location', 'locations-api' ),
			'archives'              => __( 'Location Archives', 'locations-api' ),
			'attributes'            => __( 'Location Attributes', 'locations-api' ),
			'parent_item_colon'     => __( 'Parent Location:', 'locations-api' ),
			'all_items'             => __( 'All Locations', 'locations-api' ),
			'add_new_item'          => __( 'Add New Location', 'locations-api' ),
			'add_new'               => __( 'Add New', 'locations-api' ),
			'new_item'              => __( 'New Location', 'locations-api' ),
			'edit_item'             => __( 'Edit Location', 'locations-api' ),
			'update_item'           => __( 'Update Location', 'locations-api' ),
			'view_item'             => __( 'View Location', 'locations-api' ),
			'view_items'            => __( 'View Locations', 'locations-api' ),
			'search_items'          => __( 'Search Location', 'locations-api' ),
			'not_found'             => __( 'Not found', 'locations-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'locations-api' ),
			'featured_image'        => __( 'Featured Image', 'locations-api' ),
			'set_featured_image'    => __( 'Set featured image', 'locations-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'locations-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'locations-api' ),
			'insert_into_item'      => __( 'Insert into location', 'locations-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this location', 'locations-api' ),
			'items_list'            => __( 'Locations list', 'locations-api' ),
			'items_list_navigation' => __( 'Locations list navigation', 'locations-api' ),
			'filter_items_list'     => __( 'Filter locations list', 'locations-api' ),
		);

		$args = array(
			'label'               => __( 'Location', 'locations-api' ),
			'description'         => __( 'Location with latitude and longitude meta', 'locations-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-location',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( 'locapi_location', $args );
	}

	/**
	 * Add meta boxes for location coordinates.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'locapi_location_coordinates',
			__( 'Coordinates', 'locations-api' ),
			array( __CLASS__, 'render_meta_box' ),
			'locapi_location',
			'normal',
			'default'
		);
	}

	/**
	 * Render the location coordinates meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'locapi_save_location_coordinates', 'locapi_location_coordinates_nonce' );

		$latitude  = get_post_meta( $post->ID, 'locapi_latitude', true );
		$longitude = get_post_meta( $post->ID, 'locapi_longitude', true );
		?>
		<p>
			<label for="locapi_latitude"><?php esc_html_e( 'Latitude:', 'locations-api' ); ?></label>
			<br>
			<input type="text" id="locapi_latitude" name="locapi_latitude" value="<?php echo esc_attr( $latitude ); ?>" class="widefat" />
		</p>
		<p>
			<label for="locapi_longitude"><?php esc_html_e( 'Longitude:', 'locations-api' ); ?></label>
			<br>
			<input type="text" id="locapi_longitude" name="locapi_longitude" value="<?php echo esc_attr( $longitude ); ?>" class="widefat" />
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
		if ( ! isset( $_POST['locapi_location_coordinates_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['locapi_location_coordinates_nonce'] ) ), 'locapi_save_location_coordinates' ) ) {
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

		// Save latitude.
		if ( isset( $_POST['locapi_latitude'] ) ) {
			$latitude = sanitize_text_field( wp_unslash( $_POST['locapi_latitude'] ) );
			update_post_meta( $post_id, 'locapi_latitude', $latitude );
		} else {
			delete_post_meta( $post_id, 'locapi_latitude' );
		}

		// Save longitude.
		if ( isset( $_POST['locapi_longitude'] ) ) {
			$longitude = sanitize_text_field( wp_unslash( $_POST['locapi_longitude'] ) );
			update_post_meta( $post_id, 'locapi_longitude', $longitude );
		} else {
			delete_post_meta( $post_id, 'locapi_longitude' );
		}
	}
}
