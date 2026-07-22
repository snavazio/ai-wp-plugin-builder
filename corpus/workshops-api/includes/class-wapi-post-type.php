<?php
/**
 * Custom Post Type registration for Workshops.
 *
 * @package Wapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Workshops custom post type.
 */
class Wapi_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_wapi_workshop', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Register the Workshops custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Workshops', 'Post Type General Name', 'workshops-api' ),
			'singular_name'         => _x( 'Workshop', 'Post Type Singular Name', 'workshops-api' ),
			'menu_name'             => __( 'Workshops', 'workshops-api' ),
			'name_admin_bar'        => __( 'Workshop', 'workshops-api' ),
			'archives'              => __( 'Workshop Archives', 'workshops-api' ),
			'attributes'            => __( 'Workshop Attributes', 'workshops-api' ),
			'parent_item_colon'     => __( 'Parent Workshop:', 'workshops-api' ),
			'all_items'             => __( 'All Workshops', 'workshops-api' ),
			'add_new_item'          => __( 'Add New Workshop', 'workshops-api' ),
			'add_new'               => __( 'Add New', 'workshops-api' ),
			'new_item'              => __( 'New Workshop', 'workshops-api' ),
			'edit_item'             => __( 'Edit Workshop', 'workshops-api' ),
			'update_item'           => __( 'Update Workshop', 'workshops-api' ),
			'view_item'             => __( 'View Workshop', 'workshops-api' ),
			'view_items'            => __( 'View Workshops', 'workshops-api' ),
			'search_items'          => __( 'Search Workshop', 'workshops-api' ),
			'not_found'             => __( 'Not found', 'workshops-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'workshops-api' ),
			'featured_image'        => __( 'Featured Image', 'workshops-api' ),
			'set_featured_image'    => __( 'Set featured image', 'workshops-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'workshops-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'workshops-api' ),
			'insert_into_item'      => __( 'Insert into workshop', 'workshops-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this workshop', 'workshops-api' ),
			'items_list'            => __( 'Workshops list', 'workshops-api' ),
			'items_list_navigation' => __( 'Workshops list navigation', 'workshops-api' ),
			'filter_items_list'     => __( 'Filter workshops list', 'workshops-api' ),
		);

		$args = array(
			'label'               => __( 'Workshop', 'workshops-api' ),
			'description'         => __( 'Workshops with secure date meta', 'workshops-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-calendar-alt',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rest_base'           => 'workshops',
			'show_in_rest'        => true,
		);

		register_post_type( 'wapi_workshop', $args );
	}

	/**
	 * Add meta boxes for workshop date.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'wapi_workshop_date',
			__( 'Workshop Date', 'workshops-api' ),
			array( __CLASS__, 'render_meta_box' ),
			'wapi_workshop',
			'normal',
			'default'
		);
	}

	/**
	 * Render the workshop date meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'wapi_save_workshop_date', 'workshop_meta_nonce' );

		$date = get_post_meta( $post->ID, 'workshop_date', true );
		?>
		<p>
			<label for="workshop_date"><?php esc_html_e( 'Workshop Date:', 'workshops-api' ); ?></label>
			<br>
			<input type="date" id="workshop_date" name="workshop_date" class="widefat" value="<?php echo esc_attr( $date ); ?>" />
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
		if ( ! isset( $_POST['workshop_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['workshop_meta_nonce'] ) ), 'wapi_save_workshop_date' ) ) {
			return;
		}

		// Check user capability.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		// Avoid autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Save workshop date.
		if ( isset( $_POST['workshop_date'] ) ) {
			$date = sanitize_text_field( wp_unslash( $_POST['workshop_date'] ) );
			update_post_meta( $post_id, 'workshop_date', $date );
		} else {
			delete_post_meta( $post_id, 'workshop_date' );
		}
	}
}
