<?php
/**
 * Recent Comments Widget class.
 *
 * @package Rcws
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recent Comments Widget.
 */
class RCWS_Recent_Comments_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'rcws_recent_comments_widget',
			__( 'Recent Comments', 'recent-comments-widget' ),
			array( 'description' => __( 'Lists the N most recent approved comments with escaped author and excerpt and a configurable count.', 'recent-comments-widget' ) )
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

		// Fetch most recent approved comments.
		$comments = get_comments(
			array(
				'status' => 'approve',
				'number' => $count,
				'order'  => 'DESC',
			)
		);

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}
		if ( ! empty( $comments ) ) {
			echo '<ul>';
			foreach ( $comments as $comment ) {
				echo '<li>';
				echo '<span class="rcws-comment-author">' . esc_html( get_comment_author( $comment ) ) . '</span>';
				echo '<span class="rcws-comment-excerpt">' . esc_html( get_comment_excerpt( $comment ) ) . '</span>';
				echo '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>' . esc_html__( 'No recent comments found.', 'recent-comments-widget' ) . '</p>';
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
				<?php esc_html_e( 'Heading:', 'recent-comments-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">
				<?php esc_html_e( 'Number of comments to show:', 'recent-comments-widget' ); ?>
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
