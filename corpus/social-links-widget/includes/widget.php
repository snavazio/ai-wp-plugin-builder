<?php
/**
 * Social Links Widget class.
 *
 * @package Slwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Social Links Widget.
 */
class SLWGT_Social_Links_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'slwgt_social_links',
			__( 'Social Links', 'social-links-widget' ),
			array( 'description' => __( 'Displays social profile links from the settings page.', 'social-links-widget' ) )
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
		$social_links = get_option( 'slwgt_social_links', array() );
		$networks     = array(
			'facebook'  => __( 'Facebook', 'social-links-widget' ),
			'twitter'   => __( 'Twitter', 'social-links-widget' ),
			'instagram' => __( 'Instagram', 'social-links-widget' ),
			'linkedin'  => __( 'LinkedIn', 'social-links-widget' ),
			'pinterest' => __( 'Pinterest', 'social-links-widget' ),
			'youtube'   => __( 'YouTube', 'social-links-widget' ),
		);

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}

		if ( ! empty( $social_links ) ) {
			echo '<ul class="slwgt-social-links">';
			foreach ( $networks as $network => $name ) {
				if ( ! empty( $social_links[ $network ] ) ) {
					$url = esc_url( $social_links[ $network ] );
					echo '<li><a href="' . esc_url( $url ) . '" title="' . esc_attr( $name ) . '">' . esc_html( $name ) . '</a></li>';
				}
			}
			echo '</ul>';
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
				<?php esc_html_e( 'Title:', 'social-links-widget' ); ?>
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
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}
}
