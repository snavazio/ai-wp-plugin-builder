<?php
/**
 * Movie listing shortcode handler.
 *
 * @package Mrev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [movies] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function mrev_movies_shortcode( $atts ) {
	$defaults = array(
		'genre' => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'movies' );

	// Sanitize attributes.
	$genre = sanitize_key( $atts['genre'] );

	$args = array(
		'post_type'      => 'movie',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $genre ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'mrev_genre',
				'field'    => 'slug',
				'terms'    => $genre,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No movies found.', 'movie-reviews' ) . '</p>';
	}

	ob_start();
	?>
	<div class="mrev-movies" data-genre="<?php echo esc_attr( $genre ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="mrev-movie">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="mrev-movie-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<h2 class="mrev-movie-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="mrev-movie-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php
				$genres = get_the_term_list( get_the_ID(), 'mrev_genre', '', ', ', '' );
				if ( $genres ) :
					?>
					<div class="mrev-movie-genres">
						<?php echo esc_html( $genres ); ?>
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
add_shortcode( 'movies', 'mrev_movies_shortcode' );
