<?php
/**
 * Tutorial listing shortcode handler.
 *
 * @package Tutly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [tutorials] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function tutly_tutorials_shortcode( $atts ) {
	$defaults = array(
		'difficulty' => '',
		'count'      => '10',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'tutorials' );

	// Sanitize attributes.
	$difficulty = sanitize_key( $atts['difficulty'] );
	$count      = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'tutorials',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $difficulty ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'difficulty',
				'field'    => 'slug',
				'terms'    => $difficulty,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No tutorials found.', 'tutorials' ) . '</p>';
	}

	ob_start();
	?>
	<div class="tutly-tutorials" data-difficulty="<?php echo esc_attr( $difficulty ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="tutly-tutorial">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="tutly-tutorial-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<h2 class="tutly-tutorial-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="tutly-tutorial-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php
				$difficulties = get_the_term_list( get_the_ID(), 'difficulty', '', ', ', '' );
				if ( $difficulties ) :
					?>
					<div class="tutly-tutorial-difficulty">
						<?php echo esc_html( $difficulties ); ?>
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
add_shortcode( 'tutorials', 'tutly_tutorials_shortcode' );
