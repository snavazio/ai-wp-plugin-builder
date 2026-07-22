<?php
/**
 * Tag List Widget class.
 *
 * @package Tlw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tag List Widget.
 */
class TLW_Tag_List_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'tlw_tag_list_widget',
			__( 'Tag List', 'tag-list-widget' ),
			array( 'description' => __( 'Lists the N most-used post tags as escaped links with configurable title and count.', 'tag-list-widget' ) )
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
		$count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 10;

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}

		$tags = get_terms(
			array(
				'taxonomy'   => 'post_tag',
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => $count,
				'hide_empty' => false,
			)
		);

		if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
			echo '<ul class="tlw-tag-list">';
			foreach ( $tags as $tag ) {
				echo '<li><a href="' . esc_url( get_term_link( $tag ) ) . '">' . esc_html( $tag->name ) . '</a></li>';
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
		$count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 10;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'tag-list-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">
				<?php esc_html_e( 'Number of tags to show:', 'tag-list-widget' ); ?>
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
		return $instance;
	}
}
