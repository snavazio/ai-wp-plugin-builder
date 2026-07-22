<?php
/**
 * Real Estate Properties shortcode handler.
 *
 * @package Reli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [properties] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function reli_properties_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'properties' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'property',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No properties found.', 'real-estate-listings' ) . '</p>';
	}

	ob_start();
	?>
	<div class="reli-properties" aria-label="<?php esc_attr_e( 'Real estate properties list', 'real-estate-listings' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$price     = get_post_meta( get_the_ID(), 'reli_price', true );
			$bedrooms  = get_post_meta( get_the_ID(), 'reli_bedrooms', true );
			$bathrooms = get_post_meta( get_the_ID(), 'reli_bathrooms', true );
			?>
			<div class="reli-property" itemscope itemtype="https://schema.org/RealEstateListing">
				<h3 class="reli-property-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $price ) : ?>
					<div class="reli-price" itemprop="price"><?php echo esc_html( $price ); ?></div>
				<?php endif; ?>
				<?php if ( $bedrooms ) : ?>
					<div class="reli-bedrooms" itemprop="numberOfBedrooms"><?php echo esc_html( $bedrooms ); ?></div>
				<?php endif; ?>
				<?php if ( $bathrooms ) : ?>
					<div class="reli-bathrooms" itemprop="numberOfBathrooms"><?php echo esc_html( $bathrooms ); ?></div>
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
add_shortcode( 'properties', 'reli_properties_shortcode' );
