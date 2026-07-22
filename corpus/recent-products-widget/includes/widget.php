<?php
/**
 * Recent Products Widget class.
 *
 * @package Rpwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recent Products Widget.
 */
class Rpwgt_Recent_Products_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'rpwgt_recent_products_widget',
			__( 'Recent Products', 'recent-products-widget' ),
			array( 'description' => __( 'Lists the N most recent published posts of a "product" post type if present (else posts), with escaped titles and configurable count.', 'recent-products-widget' ) )
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 5;

		// Determine post type to query.
		$post_type = post_type_exists( 'product' ) ? 'product' : 'post';

		// Fetch most recent published posts.
		$posts = get_posts(
			array(
				'posts_per_page' => $count,
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}
		if ( ! empty( $posts ) ) {
			echo '<ul>';
			foreach ( $posts as $post ) {
				echo '<li><a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( get_the_title( $post->ID ) ) . '</a></li>';
			}
			echo '</ul>';
		} else {
			echo '<p>' . esc_html__( 'No recent products found.', 'recent-products-widget' ) . '</p>';
		}
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Outputs the settings form in the admin.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 5;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Heading:', 'recent-products-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">
				<?php esc_html_e( 'Number of products to show:', 'recent-products-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" min="1" value="<?php echo esc_attr( (string) $count ); ?>">
		</p>
		<?php
	}

	/**
	 * Processes widget options to be saved.
	 *
	 * @param array $new_instance New settings.
	 * @param array $old_instance Old settings.
	 *
	 * @return array Updated settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = sanitize_text_field( wp_unslash( $new_instance['title'] ) );
		$instance['count'] = absint( wp_unslash( $new_instance['count'] ) );
		$instance['count'] = max( 1, $instance['count'] ); // Ensure at least 1.
		return $instance;
	}
}
