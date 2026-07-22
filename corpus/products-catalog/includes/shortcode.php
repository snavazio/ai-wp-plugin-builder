<?php
/**
 * Products catalog shortcode handler.
 *
 * @package Prod
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [products] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function prod_products_shortcode( $atts ) {
	$defaults = array(
		'category' => '',
		'count'    => '10',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'products' );

	// Sanitize attributes.
	$category = sanitize_key( $atts['category'] );
	$count    = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $category ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No products found.', 'products-catalog' ) . '</p>';
	}

	ob_start();
	?>
	<div class="prod-products-list" data-category="<?php echo esc_attr( $category ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="prod-product">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="prod-product-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<h2 class="prod-product-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="prod-product-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php if ( ! empty( $category ) ) : ?>
					<div class="prod-product-category">
						<?php echo esc_html__( 'Category:', 'products-catalog' ); ?>
						<?php echo esc_html( $category ); ?>
					</div>
				<?php endif; ?>
			</article>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'products', 'prod_products_shortcode' );
