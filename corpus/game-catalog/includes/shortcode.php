<?php
/**
 * Game listing shortcode handler.
 *
 * @package Gcat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [games] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function gcat_games_shortcode( $atts ) {
	$defaults = array(
		'platform' => '',
		'genre'    => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'games' );

	// Sanitize attributes.
	$platform = sanitize_key( wp_unslash( $atts['platform'] ) );
	$genre    = sanitize_key( wp_unslash( $atts['genre'] ) );

	$args = array(
		'post_type'      => 'game',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( ! empty( $platform ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'gcat_platform',
			'field'    => 'slug',
			'terms'    => $platform,
		);
	}

	if ( ! empty( $genre ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'gcat_genre',
			'field'    => 'slug',
			'terms'    => $genre,
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No games found.', 'game-catalog' ) . '</p>';
	}

	ob_start();
	?>
	<div class="gcat-games-list" data-platform="<?php echo esc_attr( $platform ); ?>" data-genre="<?php echo esc_attr( $genre ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<div class="gcat-game-item">
				<h3 class="gcat-game-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h3>
				<?php
				$platforms = get_the_term_list( get_the_ID(), 'gcat_platform', '', ', ', '' );
				if ( $platforms ) {
					echo '<p class="gcat-game-platforms"><span class="gcat-label">' . esc_html__( 'Platforms:', 'game-catalog' ) . '</span> ' . esc_html( $platforms ) . '</p>';
				}
				$genres = get_the_term_list( get_the_ID(), 'gcat_genre', '', ', ', '' );
				if ( $genres ) {
					echo '<p class="gcat-game-genres"><span class="gcat-label">' . esc_html__( 'Genres:', 'game-catalog' ) . '</span> ' . esc_html( $genres ) . '</p>';
				}
				?>
			</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'games', 'gcat_games_shortcode' );
