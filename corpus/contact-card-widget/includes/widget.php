<?php
/**
 * Contact Card Widget class.
 *
 * @package Ccw1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contact Card Widget.
 */
class CCW1_Contact_Card_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'ccw1_contact_card_widget',
			__( 'Contact Card', 'contact-card-widget' ),
			array( 'description' => __( 'Displays phone, email, and address from instance settings with configurable heading.', 'contact-card-widget' ) )
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
		$phone   = ! empty( $instance['phone'] ) ? $instance['phone'] : '';
		$email   = ! empty( $instance['email'] ) ? $instance['email'] : '';
		$address = ! empty( $instance['address'] ) ? $instance['address'] : '';

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}
		if ( ! empty( $phone ) ) {
			echo '<p>' . esc_html__( 'Phone:', 'contact-card-widget' ) . ' ' . esc_html( $phone ) . '</p>';
		}
		if ( ! empty( $email ) ) {
			echo '<p>' . esc_html__( 'Email:', 'contact-card-widget' ) . ' <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></p>';
		}
		if ( ! empty( $address ) ) {
			echo '<p>' . esc_html__( 'Address:', 'contact-card-widget' ) . ' ' . esc_html( $address ) . '</p>';
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
		$phone   = ! empty( $instance['phone'] ) ? $instance['phone'] : '';
		$email   = ! empty( $instance['email'] ) ? $instance['email'] : '';
		$address = ! empty( $instance['address'] ) ? $instance['address'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Heading:', 'contact-card-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>">
				<?php esc_html_e( 'Phone:', 'contact-card-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'phone' ) ); ?>" type="text" value="<?php echo esc_attr( $phone ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>">
				<?php esc_html_e( 'Email:', 'contact-card-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>">
				<?php esc_html_e( 'Address:', 'contact-card-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'address' ) ); ?>" type="text" value="<?php echo esc_attr( $address ); ?>">
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
		$instance['phone']   = sanitize_text_field( wp_unslash( $new_instance['phone'] ) );
		$instance['email']   = sanitize_text_field( wp_unslash( $new_instance['email'] ) );
		$instance['address'] = sanitize_text_field( wp_unslash( $new_instance['address'] ) );
		return $instance;
	}
}
