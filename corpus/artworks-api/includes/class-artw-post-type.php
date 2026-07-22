<?php
/**
 * Custom Post Type registration for Artworks.
 *
 * @package Artw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Artworks custom post type.
 */
class Artw_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_artw_artwork', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Register the Artworks custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Artworks', 'Post Type General Name', 'artworks-api' ),
			'singular_name'         => _x( 'Artwork', 'Post Type Singular Name', 'artworks-api' ),
			'menu_name'             => __( 'Artworks', 'artworks-api' ),
			'name_admin_bar'        => __( 'Artwork', 'artworks-api' ),
			'archives'              => __( 'Artwork Archives', 'artworks-api' ),
			'attributes'            => __( 'Artwork Attributes', 'artworks-api' ),
			'parent_item_colon'     => __( 'Parent Artwork:', 'artworks-api' ),
			'all_items'             => __( 'All Artworks', 'artworks-api' ),
			'add_new_item'          => __( 'Add New Artwork', 'artworks-api' ),
			'add_new'               => __( 'Add New', 'artworks-api' ),
			'new_item'              => __( 'New Artwork', 'artworks-api' ),
			'edit_item'             => __( 'Edit Artwork', 'artworks-api' ),
			'update_item'           => __( 'Update Artwork', 'artworks-api' ),
			'view_item'             => __( 'View Artwork', 'artworks-api' ),
			'view_items'            => __( 'View Artworks', 'artworks-api' ),
			'search_items'          => __( 'Search Artwork', 'artworks-api' ),
			'not_found'             => __( 'Not found', 'artworks-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'artworks-api' ),
			'featured_image'        => __( 'Featured Image', 'artworks-api' ),
			'set_featured_image'    => __( 'Set featured image', 'artworks-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'artworks-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'artworks-api' ),
			'insert_into_item'      => __( 'Insert into artwork', 'artworks-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this artwork', 'artworks-api' ),
			'items_list'            => __( 'Artworks list', 'artworks-api' ),
			'items_list_navigation' => __( 'Artworks list navigation', 'artworks-api' ),
			'filter_items_list'     => __( 'Filter artworks list', 'artworks-api' ),
		);

		$args = array(
			'label'               => __( 'Artwork', 'artworks-api' ),
			'description'         => __( 'Artworks with artist metadata', 'artworks-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-art',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rest_base'           => 'artworks',
			'show_in_rest'        => true,
		);

		register_post_type( 'artw_artwork', $args );
	}

	/**
	 * Add meta boxes for artist metadata.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'artw_artwork_artist',
			__( 'Artist', 'artworks-api' ),
			array( __CLASS__, 'render_meta_box' ),
			'artw_artwork',
			'normal',
			'default'
		);
	}

	/**
	 * Render the artist meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'artw_save_artwork_artist', 'artw_artwork_artist_nonce' );

		$artist = get_post_meta( $post->ID, 'artw_artist', true );
		?>
		<p>
			<label for="artw_artist"><?php esc_html_e( 'Artist Name:', 'artworks-api' ); ?></label>
			<br>
			<input type="text" id="artw_artist" name="artw_artist" value="<?php echo esc_attr( $artist ); ?>" class="widefat" />
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
		if ( ! isset( $_POST['artw_artwork_artist_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['artw_artwork_artist_nonce'] ) ), 'artw_save_artwork_artist' ) ) {
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

		// Save artist.
		if ( isset( $_POST['artw_artist'] ) ) {
			$artist = sanitize_text_field( wp_unslash( $_POST['artw_artist'] ) );
			update_post_meta( $post_id, 'artw_artist', $artist );
		} else {
			delete_post_meta( $post_id, 'artw_artist' );
		}
	}
}
