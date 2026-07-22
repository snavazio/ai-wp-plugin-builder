<?php
/**
 * Episode directory shortcode handler.
 *
 * @package Podc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [episodes] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function podc_episodes_shortcode( $atts ) {
	$defaults = array(
		'season' => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'episodes' );

	// Sanitize attributes.
	$season = sanitize_key( $atts['season'] );

	$args = array(
		'post_type'      => 'episode',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $season ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'season',
				'field'    => 'slug',
				'terms'    => $season,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No episodes found.', 'podcast-episodes' ) . '</p>';
	}

	ob_start();
	?>
	<div class="podc-episodes" data-season="<?php echo esc_attr( $season ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="podc-episode">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="podc-episode-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<h2 class="podc-episode-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="podc-episode-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php
				$seasons = get_the_term_list( get_the_ID(), 'season', '', ', ', '' );
				if ( $seasons ) :
					?>
					<div class="podc-episode-seasons">
						<?php echo esc_html( $seasons ); ?>
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
add_shortcode( 'episodes', 'podc_episodes_shortcode' );
