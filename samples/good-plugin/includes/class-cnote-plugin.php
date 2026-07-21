<?php
/**
 * Main plugin class for the Client Notes sample.
 *
 * @package Good_Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Client Notes CPT, an admin rating column, and a shortcode.
 */
class Cnote_Plugin {

	/**
	 * The custom post type key.
	 *
	 * @var string
	 */
	const POST_TYPE = 'cnote_note';

	/**
	 * Wire up hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'add_rating_column' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'render_rating_column' ), 10, 2 );
		add_shortcode( 'client_notes', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Load the plugin text domain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'good-plugin', false, dirname( plugin_basename( CNOTE_FILE ) ) . '/languages' );
	}

	/**
	 * Register the Client Notes custom post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'          => __( 'Client Notes', 'good-plugin' ),
			'singular_name' => __( 'Client Note', 'good-plugin' ),
			'add_new_item'  => __( 'Add New Client Note', 'good-plugin' ),
			'edit_item'     => __( 'Edit Client Note', 'good-plugin' ),
		);

		$args = array(
			'labels'       => $labels,
			'public'       => false,
			'show_ui'      => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-clipboard',
			'supports'     => array( 'title', 'editor', 'custom-fields' ),
			'has_archive'  => false,
			'rewrite'      => false,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Add a Rating column to the Client Notes admin list table.
	 *
	 * @param array $columns Existing columns.
	 * @return array Modified columns.
	 */
	public function add_rating_column( $columns ) {
		$columns['cnote_rating'] = __( 'Rating', 'good-plugin' );
		return $columns;
	}

	/**
	 * Render the Rating column value for a row.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID for the row.
	 * @return void
	 */
	public function render_rating_column( $column, $post_id ) {
		if ( 'cnote_rating' !== $column ) {
			return;
		}
		$rating = absint( get_post_meta( $post_id, 'cnote_rating', true ) );
		echo esc_html( $rating > 0 ? str_repeat( '★', min( 5, $rating ) ) : '—' );
	}

	/**
	 * Shortcode: list the most recent published client notes.
	 *
	 * Usage: [client_notes count="5"]
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Escaped HTML.
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'count' => 5,
			),
			$atts,
			'client_notes'
		);

		$query = new WP_Query(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => absint( $atts['count'] ),
				'no_found_rows'  => true,
			)
		);

		if ( ! $query->have_posts() ) {
			return '<p>' . esc_html__( 'No client notes yet.', 'good-plugin' ) . '</p>';
		}

		$out = '<ul class="cnote-list">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$out .= '<li>' . esc_html( get_the_title() ) . '</li>';
		}
		wp_reset_postdata();
		$out .= '</ul>';

		return $out;
	}
}
