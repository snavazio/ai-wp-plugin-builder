<?php
/**
 * Coupons shortcode handler.
 *
 * @package Cpns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [coupons] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function cpns_coupons_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'coupons' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'coupon',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No coupons found.', 'coupons' ) . '</p>';
	}

	ob_start();
	?>
	<div class="cpns-coupons" aria-label="<?php esc_attr_e( 'Coupons list', 'coupons' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$code   = get_post_meta( get_the_ID(), 'cpns_code', true );
			$expiry = get_post_meta( get_the_ID(), 'cpns_expiry', true );
			?>
			<div class="cpns-coupon" itemscope itemtype="https://schema.org/DiscountOffer">
				<h3 class="cpns-coupon-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $code ) : ?>
					<div class="cpns-coupon-code" itemprop="discountCode"><?php echo esc_html( $code ); ?></div>
				<?php endif; ?>
				<?php if ( $expiry ) : ?>
					<div class="cpns-coupon-expiry" itemprop="validThrough"><?php echo esc_html( $expiry ); ?></div>
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
add_shortcode( 'coupons', 'cpns_coupons_shortcode' );
