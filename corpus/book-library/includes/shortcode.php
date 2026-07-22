<?php
/**
 * Book Library shortcode handler.
 *
 * @package Blib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [library] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function blib_library_shortcode( $atts ) {
	$defaults = array(
		'author' => '',
		'genre'  => '',
		'count'  => '10',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'library' );

	// Sanitize attributes.
	$author = sanitize_key( $atts['author'] );
	$genre  = sanitize_key( $atts['genre'] );
	$count  = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'blib_book',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $author ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'blib_author',
				'field'    => 'slug',
				'terms'    => $author,
			),
		);
	}

	if ( ! empty( $genre ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'blib_genre',
				'field'    => 'slug',
				'terms'    => $genre,
			),
		);
	}

	// Handle multiple taxonomies.
	if ( ! empty( $author ) && ! empty( $genre ) ) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'blib_author',
				'field'    => 'slug',
				'terms'    => $author,
			),
			array(
				'taxonomy' => 'blib_genre',
				'field'    => 'slug',
				'terms'    => $genre,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No books found.', 'book-library' ) . '</p>';
	}

	ob_start();
	?>
	<div class="blib-library" data-author="<?php echo esc_attr( $author ); ?>" data-genre="<?php echo esc_attr( $genre ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="blib-book">
				<h2 class="blib-book-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="blib-book-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<div class="blib-book-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php
				$authors = get_the_term_list( get_the_ID(), 'blib_author', '', ', ', '' );
				if ( $authors ) :
					?>
					<div class="blib-book-authors">
						<?php echo esc_html__( 'By', 'book-library' ); ?>
						<?php echo esc_html( $authors ); ?>
					</div>
				<?php endif; ?>
				<?php
				$genres = get_the_term_list( get_the_ID(), 'blib_genre', '', ', ', '' );
				if ( $genres ) :
					?>
					<div class="blib-book-genres">
						<?php echo esc_html__( 'Genre', 'book-library' ); ?>
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
add_shortcode( 'library', 'blib_library_shortcode' );
