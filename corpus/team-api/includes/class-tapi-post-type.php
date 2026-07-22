<?php
/**
 * Custom Post Type registration for Team Members.
 *
 * @package Tapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Team Members custom post type.
 */
class Tapi_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_tapi_member', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Register the Team Members custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Team Members', 'Post Type General Name', 'team-api' ),
			'singular_name'         => _x( 'Team Member', 'Post Type Singular Name', 'team-api' ),
			'menu_name'             => __( 'Team Members', 'team-api' ),
			'name_admin_bar'        => __( 'Team Member', 'team-api' ),
			'archives'              => __( 'Team Member Archives', 'team-api' ),
			'attributes'            => __( 'Team Member Attributes', 'team-api' ),
			'parent_item_colon'     => __( 'Parent Team Member:', 'team-api' ),
			'all_items'             => __( 'All Team Members', 'team-api' ),
			'add_new_item'          => __( 'Add New Team Member', 'team-api' ),
			'add_new'               => __( 'Add New', 'team-api' ),
			'new_item'              => __( 'New Team Member', 'team-api' ),
			'edit_item'             => __( 'Edit Team Member', 'team-api' ),
			'update_item'           => __( 'Update Team Member', 'team-api' ),
			'view_item'             => __( 'View Team Member', 'team-api' ),
			'view_items'            => __( 'View Team Members', 'team-api' ),
			'search_items'          => __( 'Search Team Member', 'team-api' ),
			'not_found'             => __( 'Not found', 'team-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'team-api' ),
			'featured_image'        => __( 'Featured Image', 'team-api' ),
			'set_featured_image'    => __( 'Set featured image', 'team-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'team-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'team-api' ),
			'insert_into_item'      => __( 'Insert into team member', 'team-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this team member', 'team-api' ),
			'items_list'            => __( 'Team Members list', 'team-api' ),
			'items_list_navigation' => __( 'Team Members list navigation', 'team-api' ),
			'filter_items_list'     => __( 'Filter team members list', 'team-api' ),
		);

		$args = array(
			'label'               => __( 'Team Member', 'team-api' ),
			'description'         => __( 'Team member with role meta', 'team-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
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

		register_post_type( 'tapi_member', $args );
	}

	/**
	 * Add meta boxes for team member role.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'tapi_member_role',
			__( 'Team Role', 'team-api' ),
			array( __CLASS__, 'render_meta_box' ),
			'tapi_member',
			'normal',
			'default'
		);
	}

	/**
	 * Render the team role meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'tapi_save_member_role', 'tapi_member_role_nonce' );

		$role = get_post_meta( $post->ID, 'tapi_role', true );
		?>
		<p>
			<label for="tapi_role"><?php esc_html_e( 'Team Role:', 'team-api' ); ?></label>
			<br>
			<input type="text" id="tapi_role" name="tapi_role" value="<?php echo esc_attr( $role ); ?>" class="widefat" />
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
		if ( ! isset( $_POST['tapi_member_role_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tapi_member_role_nonce'] ) ), 'tapi_save_member_role' ) ) {
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

		// Save role.
		if ( isset( $_POST['tapi_role'] ) ) {
			$role = sanitize_text_field( wp_unslash( $_POST['tapi_role'] ) );
			update_post_meta( $post_id, 'tapi_role', $role );
		} else {
			delete_post_meta( $post_id, 'tapi_role' );
		}
	}
}
