<?php
/**
 * Countdown Widget class.
 *
 * @package Cdwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Countdown Widget.
 */
class CDWGT_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'cdwgt_widget',
			__( 'Countdown Widget', 'countdown-widget' ),
			array( 'description' => __( 'Displays the number of days remaining until a target date, with configurable title.', 'countdown-widget' ) )
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		$title       = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$target_date = ! empty( $instance['target_date'] ) ? $instance['target_date'] : '';

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}

		// Calculate days remaining.
		$days = 0;
		if ( ! empty( $target_date ) ) {
			$target = strtotime( $target_date );
			if ( false !== $target ) {
				$current = time();
				$days    = floor( ( $target - $current ) / ( 60 * 60 * 24 ) );
			}
		}

		// translators: %d is the number of days remaining.
		echo '<p>' . esc_html( sprintf( _n( 'Days remaining: %d', 'Days remaining: %d', $days, 'countdown-widget' ), $days ) ) . '</p>';
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Outputs the settings form in the admin.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title       = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$target_date = ! empty( $instance['target_date'] ) ? $instance['target_date'] : '';

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'countdown-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'target_date' ) ); ?>">
				<?php esc_html_e( 'Target Date (YYYY-MM-DD):', 'countdown-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'target_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target_date' ) ); ?>" type="text" value="<?php echo esc_attr( $target_date ); ?>">
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
		$instance                = array();
		$instance['title']       = sanitize_text_field( wp_unslash( $new_instance['title'] ) );
		$instance['target_date'] = sanitize_text_field( wp_unslash( $new_instance['target_date'] ) );
		return $instance;
	}
}
