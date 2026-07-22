<?php
/**
 * Workshop shortcode handler.
 *
 * @package Wcpm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [workshops] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function wcpm_workshops_shortcode( $atts ) {
	$defaults = array(
		'count'   => 5,
		'order'   => 'asc',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'workshops' );

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

	// Handle count=0 as all workshops.
	if ( 0 === $count ) {
		$count = -1;
	}

	// Get today's date in Y-m-d format for comparison using WordPress timezone.
	$today = current_time( 'Y-m-d' );

	$args = array(
		'post_type'      => 'workshop',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
		'meta_query'     => array(
			array(
				'key'     => 'wcpm_date',
				'value'   => $today,
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No upcoming workshops found.', 'workshop-cpt' ) . '</p>';
	}

	ob_start();
	?>
	<div class="wcpm-workshops" aria-label="<?php esc_attr_e( 'Upcoming workshops list', 'workshop-cpt' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$date  = get_post_meta( get_the_ID(), 'wcpm_date', true );
			$seats = get_post_meta( get_the_ID(), 'wcpm_seats', true );
			?>
			<div class="wcpm-workshop" itemscope itemtype="https://schema.org/Event">
				<h3 class="wcpm-workshop-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $date ) : ?>
					<div class="wcpm-date" itemprop="startDate"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ); ?></div>
				<?php endif; ?>
				<?php if ( $seats ) : ?>
					<div class="wcpm-seats" itemprop="numberOfSeats"><?php echo esc_html( $seats ); ?></div>
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
add_shortcode( 'workshops', 'wcpm_workshops_shortcode' );
