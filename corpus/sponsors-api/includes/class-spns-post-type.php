<?php
/**
 * Custom Post Type registration for Sponsors.
 *
 * @package Spns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Sponsors custom post type.
 */
class Spns_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_spns_sponsor', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Register the Sponsors custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Sponsors', 'Post Type General Name', 'sponsors-api' ),
			'singular_name'         => _x( 'Sponsor', 'Post Type Singular Name', 'sponsors-api' ),
			'menu_name'             => __( 'Sponsors', 'sponsors-api' ),
			'name_admin_bar'        => __( 'Sponsor', 'sponsors-api' ),
			'archives'              => __( 'Sponsor Archives', 'sponsors-api' ),
			'attributes'            => __( 'Sponsor Attributes', 'sponsors-api' ),
			'parent_item_colon'     => __( 'Parent Sponsor:', 'sponsors-api' ),
			'all_items'             => __( 'All Sponsors', 'sponsors-api' ),
			'add_new_item'          => __( 'Add New Sponsor', 'sponsors-api' ),
			'add_new'               => __( 'Add New', 'sponsors-api' ),
			'new_item'              => __( 'New Sponsor', 'sponsors-api' ),
			'edit_item'             => __( 'Edit Sponsor', 'sponsors-api' ),
			'update_item'           => __( 'Update Sponsor', 'sponsors-api' ),
			'view_item'             => __( 'View Sponsor', 'sponsors-api' ),
			'view_items'            => __( 'View Sponsors', 'sponsors-api' ),
			'search_items'          => __( 'Search Sponsor', 'sponsors-api' ),
			'not_found'             => __( 'Not found', 'sponsors-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'sponsors-api' ),
			'featured_image'        => __( 'Featured Image', 'sponsors-api' ),
			'set_featured_image'    => __( 'Set featured image', 'sponsors-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'sponsors-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'sponsors-api' ),
			'insert_into_item'      => __( 'Insert into sponsor', 'sponsors-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this sponsor', 'sponsors-api' ),
			'items_list'            => __( 'Sponsors list', 'sponsors-api' ),
			'items_list_navigation' => __( 'Sponsors list navigation', 'sponsors-api' ),
			'filter_items_list'     => __( 'Filter sponsors list', 'sponsors-api' ),
		);

		$args = array(
			'label'               => __( 'Sponsor', 'sponsors-api' ),
			'description'         => __( 'Published sponsors with website meta', 'sponsors-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-groups',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( 'spns_sponsor', $args );
	}

	/**
	 * Add meta boxes for sponsor website.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'spns_sponsor_website',
			__( 'Website', 'sponsors-api' ),
			array( __CLASS__, 'render_meta_box' ),
			'spns_sponsor',
			'normal',
			'default'
		);
	}

	/**
	 * Render the sponsor website meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'spns_save_sponsor_website', 'spns_sponsor_website_nonce' );

		$website = get_post_meta( $post->ID, 'spns_website', true );
		?>
		<p>
			<label for="spns_website"><?php esc_html_e( 'Website URL:', 'sponsors-api' ); ?></label>
			<br>
			<input type="url" id="spns_website" name="spns_website" class="widefat" value="<?php echo esc_url( $website ); ?>" />
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
		if ( ! isset( $_POST['spns_sponsor_website_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['spns_sponsor_website_nonce'] ) ), 'spns_save_sponsor_website' ) ) {
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

		// Save website URL.
		if ( isset( $_POST['spns_website'] ) ) {
			$website = esc_url_raw( wp_unslash( $_POST['spns_website'] ) );
			update_post_meta( $post_id, 'spns_website', $website );
		} else {
			delete_post_meta( $post_id, 'spns_website' );
		}
	}
}
