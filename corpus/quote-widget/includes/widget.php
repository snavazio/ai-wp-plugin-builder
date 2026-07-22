<?php
/**
 * Quote Widget class.
 *
 * @package Qwid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Quote Widget.
 */
class QWID_Quote_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'qwid_quote_widget',
			__( 'Quote Widget', 'quote-widget' ),
			array( 'description' => __( 'Displays a quote with a configurable title and author, featuring full output escaping and input sanitization.', 'quote-widget' ) )
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		$title  = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$quote  = ! empty( $instance['quote'] ) ? $instance['quote'] : '';
		$author = ! empty( $instance['author'] ) ? $instance['author'] : '';

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}
		if ( ! empty( $quote ) ) {
			echo '<blockquote>';
			echo '<p>' . esc_html( $quote ) . '</p>';
			if ( ! empty( $author ) ) {
				echo '<footer>' . esc_html( $author ) . '</footer>';
			}
			echo '</blockquote>';
		}
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Outputs the settings form in the admin.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title  = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$quote  = ! empty( $instance['quote'] ) ? $instance['quote'] : '';
		$author = ! empty( $instance['author'] ) ? $instance['author'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Widget Title:', 'quote-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'quote' ) ); ?>">
				<?php esc_html_e( 'Quote:', 'quote-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'quote' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'quote' ) ); ?>" type="text" value="<?php echo esc_attr( $quote ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>">
				<?php esc_html_e( 'Author:', 'quote-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'author' ) ); ?>" type="text" value="<?php echo esc_attr( $author ); ?>">
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
		$instance           = array();
		$instance['title']  = sanitize_text_field( wp_unslash( $new_instance['title'] ) );
		$instance['quote']  = sanitize_text_field( wp_unslash( $new_instance['quote'] ) );
		$instance['author'] = sanitize_text_field( wp_unslash( $new_instance['author'] ) );
		return $instance;
	}
}
