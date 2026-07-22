<?php
/**
 * Comics series shortcode handler.
 *
 * @package Comics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [comics_series] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function comics_series_shortcode( $atts ) {
	$defaults = array(
		'series' => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'comics_series' );

	// Sanitize attributes.
	$series = sanitize_key( $atts['series'] );

	$args = array(
		'post_type'      => 'comic',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $series ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'comics_series',
				'field'    => 'slug',
				'terms'    => $series,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No comics found in this series.', 'comics' ) . '</p>';
	}

	ob_start();
	?>
	<div class="comics-series" data-series="<?php echo esc_attr( $series ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="comics-series-item">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="comics-series-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<h2 class="comics-series-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="comics-series-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php
				$publishers = get_the_term_list( get_the_ID(), 'comics_publisher', '', ', ', '' );
				if ( $publishers ) :
					?>
					<div class="comics-series-publishers">
						<?php echo esc_html( $publishers ); ?>
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
add_shortcode( 'comics_series', 'comics_series_shortcode' );
