<?php
/**
 * Events shortcode handler.
 *
 * @package Evcal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [events] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function evcal_events_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'orderby' => 'date',
		'order'   => 'ASC',
	);

	$atts = shortcode_atts( $defaults, $atts, 'events' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$orderby = sanitize_key( $atts['orderby'] );
	$order   = sanitize_key( $atts['order'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	// Build query for upcoming events (start date >= today).
	$today = gmdate( 'Y-m-d' ); // Replaced date() with gmdate() to avoid timezone issues.
	$args  = array(
		'post_type'      => 'event',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
		'meta_query'     => array(
			array(
				'key'     => 'evcal_start_date',
				'value'   => $today,
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No upcoming events found.', 'events-calendar' ) . '</p>';
	}

	ob_start();
	?>
	<div class="evcal-events" aria-label="<?php esc_attr_e( 'Upcoming Events', 'events-calendar' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$start_date = get_post_meta( get_the_ID(), 'evcal_start_date', true );
			$location   = get_post_meta( get_the_ID(), 'evcal_location', true );
			?>
			<div class="evcal-event" itemscope itemtype="https://schema.org/Event">
				<h3 class="evcal-event-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $start_date ) : ?>
					<div class="evcal-event-date" itemprop="startDate">
						<?php echo esc_html( $start_date ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $location ) : ?>
					<div class="evcal-event-location" itemprop="location">
						<?php echo esc_html( $location ); ?>
					</div>
				<?php endif; ?>
				<div class="evcal-event-content" itemprop="description">
					<?php the_excerpt(); ?>
				</div>
			</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'events', 'evcal_events_shortcode' );
