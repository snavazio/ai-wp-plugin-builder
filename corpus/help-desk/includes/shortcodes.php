<?php
/**
 * Help Tickets shortcode handler.
 *
 * @package Help
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [help_tickets] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function help_tickets_shortcode( $atts ) {
	$defaults = array(
		'status' => '',
		'count'  => '10',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'help_tickets' );

	// Sanitize attributes.
	$status = sanitize_key( $atts['status'] );
	$count  = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'help_ticket',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $status ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'help_status',
				'field'    => 'slug',
				'terms'    => $status,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No help tickets found.', 'help-desk' ) . '</p>';
	}

	ob_start();
	?>
	<div class="help-tickets" data-status="<?php echo esc_attr( $status ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="help-ticket">
				<h2 class="help-ticket-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="help-ticket-content">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<div class="help-ticket-status">
					<?php
					$statuses = get_the_term_list( get_the_ID(), 'help_status', '', ', ', '' );
					if ( $statuses ) {
						echo esc_html( $statuses );
					}
					?>
				</div>
			</article>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'help_tickets', 'help_tickets_shortcode' );
