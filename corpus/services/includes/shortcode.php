<?php
/**
 * Services shortcode handler.
 *
 * @package Srvs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [services] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function srvs_services_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'services' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'service',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No services found.', 'services' ) . '</p>';
	}

	ob_start();
	?>
	<div class="srvs-services" aria-label="<?php esc_attr_e( 'Services list', 'services' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$price    = get_post_meta( get_the_ID(), 'srvs_price', true );
			$icon_url = get_post_meta( get_the_ID(), 'srvs_icon', true );
			?>
			<div class="srvs-service" itemscope itemtype="https://schema.org/Service">
				<h3 class="srvs-service-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $price ) : ?>
					<div class="srvs-price" itemprop="price"><?php echo esc_html( $price ); ?></div>
				<?php endif; ?>
				<?php if ( $icon_url ) : ?>
					<div class="srvs-icon" itemprop="image">
						<?php echo esc_html( $icon_url ); ?>
					</div>
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
add_shortcode( 'services', 'srvs_services_shortcode' );
