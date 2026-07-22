<?php
/**
 * Custom Post Type registration for Quotes.
 *
 * @package Qapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Quotes custom post type.
 */
class Qapi_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_qapi_quote', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Register the Quotes custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Quotes', 'Post Type General Name', 'quotes-api' ),
			'singular_name'         => _x( 'Quote', 'Post Type Singular Name', 'quotes-api' ),
			'menu_name'             => __( 'Quotes', 'quotes-api' ),
			'name_admin_bar'        => __( 'Quote', 'quotes-api' ),
			'archives'              => __( 'Quote Archives', 'quotes-api' ),
			'attributes'            => __( 'Quote Attributes', 'quotes-api' ),
			'parent_item_colon'     => __( 'Parent Quote:', 'quotes-api' ),
			'all_items'             => __( 'All Quotes', 'quotes-api' ),
			'add_new_item'          => __( 'Add New Quote', 'quotes-api' ),
			'add_new'               => __( 'Add New', 'quotes-api' ),
			'new_item'              => __( 'New Quote', 'quotes-api' ),
			'edit_item'             => __( 'Edit Quote', 'quotes-api' ),
			'update_item'           => __( 'Update Quote', 'quotes-api' ),
			'view_item'             => __( 'View Quote', 'quotes-api' ),
			'view_items'            => __( 'View Quotes', 'quotes-api' ),
			'search_items'          => __( 'Search Quote', 'quotes-api' ),
			'not_found'             => __( 'Not found', 'quotes-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'quotes-api' ),
			'featured_image'        => __( 'Featured Image', 'quotes-api' ),
			'set_featured_image'    => __( 'Set featured image', 'quotes-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'quotes-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'quotes-api' ),
			'insert_into_item'      => __( 'Insert into quote', 'quotes-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this quote', 'quotes-api' ),
			'items_list'            => __( 'Quotes list', 'quotes-api' ),
			'items_list_navigation' => __( 'Quotes list navigation', 'quotes-api' ),
			'filter_items_list'     => __( 'Filter quotes list', 'quotes-api' ),
		);

		$args = array(
			'label'               => __( 'Quote', 'quotes-api' ),
			'description'         => __( 'Quote with author meta', 'quotes-api' ),
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

		register_post_type( 'qapi_quote', $args );
	}

	/**
	 * Add meta boxes for quote author.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'qapi_quote_author',
			__( 'Author', 'quotes-api' ),
			array( __CLASS__, 'render_meta_box' ),
			'qapi_quote',
			'normal',
			'default'
		);
	}

	/**
	 * Render the quote author meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'qapi_save_quote_author', 'quote_meta_nonce' );

		$author = get_post_meta( $post->ID, 'qapi_author', true );
		?>
		<p>
			<label for="qapi_author"><?php esc_html_e( 'Author Name:', 'quotes-api' ); ?></label>
			<br>
			<input type="text" id="qapi_author" name="qapi_author" value="<?php echo esc_attr( $author ); ?>" class="widefat" />
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
		if ( ! isset( $_POST['quote_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['quote_meta_nonce'] ) ), 'qapi_save_quote_author' ) ) {
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
		if ( isset( $_POST['qapi_author'] ) ) {
			$author = sanitize_text_field( wp_unslash( $_POST['qapi_author'] ) );
			update_post_meta( $post_id, 'qapi_author', $author );
		} else {
			delete_post_meta( $post_id, 'qapi_author' );
		}
	}
}
