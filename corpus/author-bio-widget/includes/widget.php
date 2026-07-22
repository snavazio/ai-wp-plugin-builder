<?php
/**
 * Author Bio Widget class.
 *
 * @package Abio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Author Bio Widget.
 */
class ABIO_Author_Bio_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'abio_author_bio_widget',
			__( 'Author Bio', 'author-bio-widget' ),
			array( 'description' => __( 'Displays the post author\'s name and description in the sidebar on single posts with a customizable heading.', 'author-bio-widget' ) )
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

		// Get author info for current post.
		$author_id   = get_the_author_meta( 'ID' );
		$author_name = get_the_author_meta( 'display_name' );
		$author_desc = get_the_author_meta( 'description' );

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}
		if ( ! empty( $author_name ) ) {
			echo '<h3>' . esc_html( $author_name ) . '</h3>';
			if ( ! empty( $author_desc ) ) {
				echo '<p>' . esc_html( $author_desc ) . '</p>';
			}
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
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Heading:', 'author-bio-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
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
		return $instance;
	}
}
