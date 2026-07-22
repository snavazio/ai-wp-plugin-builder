<?php
/**
 * Rentals list shortcode handler.
 *
 * @package Rent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [rentals] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function rent_rentals_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'rentals' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'rental',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No rentals found.', 'rentals' ) . '</p>';
	}

	ob_start();
	?>
	<div class="rent-rentals" aria-label="<?php esc_attr_e( 'Rentals', 'rentals' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$price    = get_post_meta( get_the_ID(), 'rent_price', true );
			$bedrooms = get_post_meta( get_the_ID(), 'rent_bedrooms', true );
			?>
			<div class="rent-rental" itemscope itemtype="https://schema.org/RealEstateListing">
				<h3 class="rent-rental-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $price ) : ?>
					<div class="rent-rental-price" itemprop="price">
						<?php echo esc_html( $price ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $bedrooms ) : ?>
					<div class="rent-rental-bedrooms" itemprop="numberOfBedrooms">
						<?php // translators: %d is the number of bedrooms. ?>
						<?php echo esc_html( sprintf( _n( '%d Bedroom', '%d Bedrooms', $bedrooms, 'rentals' ), $bedrooms ) ); ?>
					</div>
				<?php endif; ?>
				<div class="rent-rental-content" itemprop="description">
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
add_shortcode( 'rentals', 'rent_rentals_shortcode' );
