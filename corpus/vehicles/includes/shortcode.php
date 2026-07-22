<?php
/**
 * Vehicles shortcode handler.
 *
 * @package Vclt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [vehicles] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function vclt_vehicles_shortcode( $atts ) {
	$defaults = array(
		'count' => 10,
	);
	$atts     = shortcode_atts( $defaults, $atts, 'vehicles' );

	// Sanitize attributes.
	$count = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'vehicle',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No vehicles found.', 'vehicles' ) . '</p>';
	}

	ob_start();
	?>
	<div class="vclt-vehicles-list" aria-label="<?php esc_attr_e( 'Vehicles directory', 'vehicles' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$make  = get_post_meta( get_the_ID(), 'vclt_make', true );
			$model = get_post_meta( get_the_ID(), 'vclt_model', true );
			$year  = get_post_meta( get_the_ID(), 'vclt_year', true );
			?>
			<div class="vclt-vehicle" itemscope itemtype="https://schema.org/Car">
				<h3 class="vclt-vehicle-title" itemprop="name"><?php echo esc_html( $year . ' ' . $make . ' ' . $model ); ?></h3>
				<?php if ( $make || $model || $year ) : ?>
					<div class="vclt-vehicle-meta" itemprop="model">
						<?php
						$meta_parts = array();
						if ( $year ) {
							$meta_parts[] = esc_html( $year );
						}
						if ( $make ) {
							$meta_parts[] = esc_html( $make );
						}
						if ( $model ) {
							$meta_parts[] = esc_html( $model );
						}
						echo esc_html( implode( ' ', $meta_parts ) );
						?>
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
add_shortcode( 'vehicles', 'vclt_vehicles_shortcode' );
