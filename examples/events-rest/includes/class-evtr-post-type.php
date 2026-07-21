<?php
/**
 * Custom Post Type registration for Events.
 *
 * @package Evtr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Events custom post type.
 */
class Evtr_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_evtr_event', array( __CLASS__, 'save_meta_box' ), 10, 2 );
		add_filter( 'manage_evtr_event_posts_columns', array( __CLASS__, 'add_admin_columns' ) );
		add_action( 'manage_evtr_event_posts_custom_column', array( __CLASS__, 'render_admin_columns' ), 10, 2 );
	}

	/**
	 * Register the Events custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Events', 'Post Type General Name', 'events-rest' ),
			'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'events-rest' ),
			'menu_name'             => __( 'Events', 'events-rest' ),
			'name_admin_bar'        => __( 'Event', 'events-rest' ),
			'archives'              => __( 'Event Archives', 'events-rest' ),
			'attributes'            => __( 'Event Attributes', 'events-rest' ),
			'parent_item_colon'     => __( 'Parent Event:', 'events-rest' ),
			'all_items'             => __( 'All Events', 'events-rest' ),
			'add_new_item'          => __( 'Add New Event', 'events-rest' ),
			'add_new'               => __( 'Add New', 'events-rest' ),
			'new_item'              => __( 'New Event', 'events-rest' ),
			'edit_item'             => __( 'Edit Event', 'events-rest' ),
			'update_item'           => __( 'Update Event', 'events-rest' ),
			'view_item'             => __( 'View Event', 'events-rest' ),
			'view_items'            => __( 'View Events', 'events-rest' ),
			'search_items'          => __( 'Search Event', 'events-rest' ),
			'not_found'             => __( 'Not found', 'events-rest' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'events-rest' ),
			'featured_image'        => __( 'Featured Image', 'events-rest' ),
			'set_featured_image'    => __( 'Set featured image', 'events-rest' ),
			'remove_featured_image' => __( 'Remove featured image', 'events-rest' ),
			'use_featured_image'    => __( 'Use as featured image', 'events-rest' ),
			'insert_into_item'      => __( 'Insert into event', 'events-rest' ),
			'uploaded_to_this_item' => __( 'Uploaded to this event', 'events-rest' ),
			'items_list'            => __( 'Events list', 'events-rest' ),
			'items_list_navigation' => __( 'Events list navigation', 'events-rest' ),
			'filter_items_list'     => __( 'Filter events list', 'events-rest' ),
		);

		$args = array(
			'label'               => __( 'Event', 'events-rest' ),
			'description'         => __( 'Events with date and location', 'events-rest' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-calendar',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( 'evtr_event', $args );
	}

	/**
	 * Add meta boxes for event fields.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'evtr_event_details',
			__( 'Event Details', 'events-rest' ),
			array( __CLASS__, 'render_meta_box' ),
			'evtr_event',
			'normal',
			'default'
		);
	}

	/**
	 * Render the event details meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'evtr_save_event_meta', 'evtr_event_meta_nonce' );

		$event_date     = get_post_meta( $post->ID, 'evtr_event_date', true );
		$event_location = get_post_meta( $post->ID, 'evtr_event_location', true );
		?>
		<p>
			<label for="evtr_event_date"><?php esc_html_e( 'Event Date:', 'events-rest' ); ?></label>
			<br>
			<input type="text" id="evtr_event_date" name="evtr_event_date" value="<?php echo esc_attr( $event_date ); ?>" class="widefat" />
		</p>
		<p>
			<label for="evtr_event_location"><?php esc_html_e( 'Event Location:', 'events-rest' ); ?></label>
			<br>
			<input type="text" id="evtr_event_location" name="evtr_event_location" value="<?php echo esc_attr( $event_location ); ?>" class="widefat" />
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
	public static function save_meta_box( $post_id, $post ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// Verify nonce.
		if ( ! isset( $_POST['evtr_event_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['evtr_event_meta_nonce'] ) ), 'evtr_save_event_meta' ) ) {
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

		// Save event date.
		if ( isset( $_POST['evtr_event_date'] ) ) {
			$event_date = sanitize_text_field( wp_unslash( $_POST['evtr_event_date'] ) );
			update_post_meta( $post_id, 'evtr_event_date', $event_date );
		} else {
			delete_post_meta( $post_id, 'evtr_event_date' );
		}

		// Save event location.
		if ( isset( $_POST['evtr_event_location'] ) ) {
			$event_location = sanitize_text_field( wp_unslash( $_POST['evtr_event_location'] ) );
			update_post_meta( $post_id, 'evtr_event_location', $event_location );
		} else {
			delete_post_meta( $post_id, 'evtr_event_location' );
		}
	}

	/**
	 * Add custom admin columns.
	 *
	 * @param array $columns Existing columns.
	 * @return array Modified columns.
	 */
	public static function add_admin_columns( $columns ) {
		$new_columns = array();
		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;
			if ( 'title' === $key ) {
				$new_columns['evtr_event_date']     = __( 'Event Date', 'events-rest' );
				$new_columns['evtr_event_location'] = __( 'Event Location', 'events-rest' );
			}
		}
		return $new_columns;
	}

	/**
	 * Render custom admin columns.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public static function render_admin_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'evtr_event_date':
				$event_date = get_post_meta( $post_id, 'evtr_event_date', true );
				echo esc_html( $event_date ? $event_date : '—' );
				break;

			case 'evtr_event_location':
				$event_location = get_post_meta( $post_id, 'evtr_event_location', true );
				echo esc_html( $event_location ? $event_location : '—' );
				break;
		}
	}
}
