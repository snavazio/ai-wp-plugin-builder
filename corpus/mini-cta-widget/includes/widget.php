<?php
/**
 * Mini CTA Widget class.
 *
 * @package Mctw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mini CTA Widget.
 */
class MCTW_CTA_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mctw_cta_widget',
			__( 'Mini CTA', 'mini-cta-widget' ),
			array( 'description' => __( 'Displays a heading, message, and button with customizable text and URL.', 'mini-cta-widget' ) )
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		$title        = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$message      = ! empty( $instance['message'] ) ? $instance['message'] : '';
		$button_label = ! empty( $instance['button_label'] ) ? $instance['button_label'] : '';
		$button_url   = ! empty( $instance['button_url'] ) ? $instance['button_url'] : '';

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}
		if ( ! empty( $message ) ) {
			echo '<p>' . esc_html( $message ) . '</p>';
		}
		if ( ! empty( $button_label ) && ! empty( $button_url ) ) {
			echo '<p><a href="' . esc_url( $button_url ) . '" class="mctw-button">' . esc_html( $button_label ) . '</a></p>';
		}
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Outputs the settings form in the admin.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title        = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$message      = ! empty( $instance['message'] ) ? $instance['message'] : '';
		$button_label = ! empty( $instance['button_label'] ) ? $instance['button_label'] : '';
		$button_url   = ! empty( $instance['button_url'] ) ? $instance['button_url'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Heading:', 'mini-cta-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'message' ) ); ?>">
				<?php esc_html_e( 'Message:', 'mini-cta-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'message' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'message' ) ); ?>" type="text" value="<?php echo esc_attr( $message ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>">
				<?php esc_html_e( 'Button Label:', 'mini-cta-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_label' ) ); ?>" type="text" value="<?php echo esc_attr( $button_label ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>">
				<?php esc_html_e( 'Button URL:', 'mini-cta-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_url' ) ); ?>" type="url" value="<?php echo esc_url( $button_url ); ?>">
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
		$instance                 = array();
		$instance['title']        = sanitize_text_field( wp_unslash( $new_instance['title'] ) );
		$instance['message']      = sanitize_text_field( wp_unslash( $new_instance['message'] ) );
		$instance['button_label'] = sanitize_text_field( wp_unslash( $new_instance['button_label'] ) );
		$instance['button_url']   = esc_url_raw( wp_unslash( $new_instance['button_url'] ) );
		return $instance;
	}
}
