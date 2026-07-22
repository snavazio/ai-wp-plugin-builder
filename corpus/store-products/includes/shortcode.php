<?php
/**
 * Products list shortcode handler.
 *
 * @package Strp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [store] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function strp_store_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'store' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No products found.', 'store-products' ) . '</p>';
	}

	ob_start();
	?>
	<div class="strp-products" aria-label="<?php esc_attr_e( 'Products', 'store-products' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$price = get_post_meta( get_the_ID(), 'strp_price', true );
			$sku   = get_post_meta( get_the_ID(), 'strp_sku', true );
			?>
			<div class="strp-product" itemscope itemtype="https://schema.org/Product">
				<h3 class="strp-product-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $price ) : ?>
					<div class="strp-product-price" itemprop="price">
						<?php echo esc_html( $price ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $sku ) : ?>
					<div class="strp-product-sku" itemprop="sku">
						<?php echo esc_html( $sku ); ?>
					</div>
				<?php endif; ?>
				<div class="strp-product-content" itemprop="description">
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
add_shortcode( 'store', 'strp_store_shortcode' );
