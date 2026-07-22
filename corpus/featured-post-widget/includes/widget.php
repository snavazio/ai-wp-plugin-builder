<?php
/**
 * Featured Post Widget class.
 *
 * @package Fpw1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Featured Post Widget.
 */
class Fpw1_Featured_Post_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'fpw1_featured_post_widget',
			__( 'Featured Post', 'featured-post-widget' ),
			array( 'description' => __( 'Displays the title and excerpt of a selected published post with configurable title.', 'featured-post-widget' ) )
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		$title   = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$post_id = ! empty( $instance['post_id'] ) ? absint( $instance['post_id'] ) : 0;

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}

		if ( $post_id > 0 ) {
			$post = get_post( $post_id );
			if ( $post && 'publish' === $post->post_status ) {
				echo '<h3><a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( get_the_title( $post_id ) ) . '</a></h3>';
				echo '<p>' . esc_html( get_the_excerpt( $post_id ) ) . '</p>';
			} else {
				echo '<p>' . esc_html__( 'Featured post not found.', 'featured-post-widget' ) . '</p>';
			}
		} else {
			echo '<p>' . esc_html__( 'No post selected.', 'featured-post-widget' ) . '</p>';
		}
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Outputs the settings form in the admin.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title   = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$post_id = ! empty( $instance['post_id'] ) ? absint( $instance['post_id'] ) : 0;

		// Get published posts for dropdown.
		$posts = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Heading:', 'featured-post-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>">
				<?php esc_html_e( 'Select a post:', 'featured-post-widget' ); ?>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_id' ) ); ?>">
				<option value="0"><?php esc_html_e( 'Select a post', 'featured-post-widget' ); ?></option>
				<?php foreach ( $posts as $post ) : ?>
					<option value="<?php echo esc_attr( (string) $post->ID ); ?>" <?php selected( $post_id, $post->ID ); ?>>
						<?php echo esc_html( $post->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
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
		$instance            = array();
		$instance['title']   = sanitize_text_field( wp_unslash( $new_instance['title'] ) );
		$instance['post_id'] = absint( wp_unslash( $new_instance['post_id'] ) );
		return $instance;
	}
}
