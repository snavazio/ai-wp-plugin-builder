<?php
/**
 * Gigs shortcode handler.
 *
 * @package Gigc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [gigs] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function gigc_gigs_shortcode( $atts ) {
	$defaults = array(
		'count'   => 5,
		'order'   => 'asc',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'gigs' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	// Validate order.
	if ( ! in_array( $order, array( 'asc', 'desc' ), true ) ) {
		$order = 'asc';
	}

	// Validate orderby.
	if ( ! in_array( $orderby, array( 'date', 'title' ), true ) ) {
		$orderby = 'date';
	}

	// Handle count=0 as all gigs.
	if ( 0 === $count ) {
		$count = -1;
	}

	// Get today's date in Y-m-d format for comparison using WordPress timezone.
	$today = current_time( 'Y-m-d' );

	$args = array(
		'post_type'      => 'gigc_gig',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
		'meta_query'     => array(
			array(
				'key'     => 'gigc_date',
				'value'   => $today,
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No upcoming gigs found.', 'gigs' ) . '</p>';
	}

	ob_start();
	?>
	<div class="gigc-gigs" aria-label="<?php esc_attr_e( 'Upcoming gigs list', 'gigs' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$venue = get_post_meta( get_the_ID(), 'gigc_venue', true );
			$date  = get_post_meta( get_the_ID(), 'gigc_date', true );
			?>
			<div class="gigc-gig" itemscope itemtype="https://schema.org/Event">
				<h3 class="gigc-gig-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $venue ) : ?>
					<div class="gigc-venue" itemprop="location"><?php echo esc_html( $venue ); ?></div>
				<?php endif; ?>
				<?php if ( $date ) : ?>
					<div class="gigc-date" itemprop="startDate"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ); ?></div>
				<?php endif; ?>
			</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'gigs', 'gigc_gigs_shortcode' );
