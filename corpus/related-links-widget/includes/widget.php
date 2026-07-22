<?php
/**
 * Related Links Widget class.
 *
 * @package Rlwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Related Links Widget.
 */
class Rlwgt_Related_Links_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'rlwgt_related_links_widget',
			__( 'Related Links', 'related-links-widget' ),
			array( 'description' => __( 'Displays a configurable list of label+URL link pairs from instance settings, all escaped.', 'related-links-widget' ) )
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
		$link1_label = ! empty( $instance['link1_label'] ) ? $instance['link1_label'] : '';
		$link1_url   = ! empty( $instance['link1_url'] ) ? $instance['link1_url'] : '';
		$link2_label = ! empty( $instance['link2_label'] ) ? $instance['link2_label'] : '';
		$link2_url   = ! empty( $instance['link2_url'] ) ? $instance['link2_url'] : '';
		$link3_label = ! empty( $instance['link3_label'] ) ? $instance['link3_label'] : '';
		$link3_url   = ! empty( $instance['link3_url'] ) ? $instance['link3_url'] : '';

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}

		$links = array(
			array(
				'label' => $link1_label,
				'url'   => $link1_url,
			),
			array(
				'label' => $link2_label,
				'url'   => $link2_url,
			),
			array(
				'label' => $link3_label,
				'url'   => $link3_url,
			),
		);

		foreach ( $links as $link ) {
			if ( ! empty( $link['label'] ) && ! empty( $link['url'] ) ) {
				echo '<p><a href="' . esc_url( $link['url'] ) . '">' . esc_html( $link['label'] ) . '</a></p>';
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
		$title       = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$link1_label = ! empty( $instance['link1_label'] ) ? $instance['link1_label'] : '';
		$link1_url   = ! empty( $instance['link1_url'] ) ? $instance['link1_url'] : '';
		$link2_label = ! empty( $instance['link2_label'] ) ? $instance['link2_label'] : '';
		$link2_url   = ! empty( $instance['link2_url'] ) ? $instance['link2_url'] : '';
		$link3_label = ! empty( $instance['link3_label'] ) ? $instance['link3_label'] : '';
		$link3_url   = ! empty( $instance['link3_url'] ) ? $instance['link3_url'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'related-links-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link1_label' ) ); ?>">
				<?php esc_html_e( 'Link 1 Label:', 'related-links-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link1_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link1_label' ) ); ?>" type="text" value="<?php echo esc_attr( $link1_label ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link1_url' ) ); ?>">
				<?php esc_html_e( 'Link 1 URL:', 'related-links-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link1_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link1_url' ) ); ?>" type="url" value="<?php echo esc_url( $link1_url ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link2_label' ) ); ?>">
				<?php esc_html_e( 'Link 2 Label:', 'related-links-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link2_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link2_label' ) ); ?>" type="text" value="<?php echo esc_attr( $link2_label ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link2_url' ) ); ?>">
				<?php esc_html_e( 'Link 2 URL:', 'related-links-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link2_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link2_url' ) ); ?>" type="url" value="<?php echo esc_url( $link2_url ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link3_label' ) ); ?>">
				<?php esc_html_e( 'Link 3 Label:', 'related-links-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link3_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link3_label' ) ); ?>" type="text" value="<?php echo esc_attr( $link3_label ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link3_url' ) ); ?>">
				<?php esc_html_e( 'Link 3 URL:', 'related-links-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link3_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link3_url' ) ); ?>" type="url" value="<?php echo esc_url( $link3_url ); ?>">
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
		$instance['link1_label'] = sanitize_text_field( wp_unslash( $new_instance['link1_label'] ) );
		$instance['link1_url']   = esc_url_raw( wp_unslash( $new_instance['link1_url'] ) );
		$instance['link2_label'] = sanitize_text_field( wp_unslash( $new_instance['link2_label'] ) );
		$instance['link2_url']   = esc_url_raw( wp_unslash( $new_instance['link2_url'] ) );
		$instance['link3_label'] = sanitize_text_field( wp_unslash( $new_instance['link3_label'] ) );
		$instance['link3_url']   = esc_url_raw( wp_unslash( $new_instance['link3_url'] ) );
		return $instance;
	}
}
