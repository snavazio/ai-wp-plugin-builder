<?php
/**
 * Popular Posts Widget class.
 *
 * @package Ppwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Popular Posts Widget.
 */
class PPWGT_Popular_Posts_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'ppwgt_popular_posts_widget',
			__( 'Popular Posts', 'popular-posts-widget' ),
			array( 'description' => __( 'Lists the N most-commented published posts with configurable count and heading.', 'popular-posts-widget' ) )
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

		// Fetch most commented posts.
		$posts = get_posts(
			array(
				'posts_per_page' => $count,
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'orderby'        => 'comment_count',
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
			echo '<p>' . esc_html__( 'No popular posts found.', 'popular-posts-widget' ) . '</p>';
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
				<?php esc_html_e( 'Heading:', 'popular-posts-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">
				<?php esc_html_e( 'Number of posts to show:', 'popular-posts-widget' ); ?>
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
