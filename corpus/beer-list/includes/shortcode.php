<?php
/**
 * Beer listing shortcode handler.
 *
 * @package Beers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [beers] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function beers_beers_shortcode( $atts ) {
	$defaults = array(
		'style' => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'beers' );

	// Sanitize attributes.
	$style = sanitize_key( wp_unslash( $atts['style'] ) );

	$args = array(
		'post_type'      => 'beer',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( ! empty( $style ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'style',
				'field'    => 'slug',
				'terms'    => $style,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No beers found.', 'beer-list' ) . '</p>';
	}

	ob_start();
	?>
	<div class="beers-list" data-style="<?php echo esc_attr( $style ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<div class="beers-item">
				<h3 class="beers-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h3>
				<?php
				$abv = get_post_meta( get_the_ID(), 'beers_abv', true );
				if ( $abv ) {
					echo '<p class="beers-abv"><span class="beers-label">' . esc_html__( 'ABV:', 'beer-list' ) . '</span> ' . esc_html( $abv ) . '%</p>';
				}
				$styles = get_the_term_list( get_the_ID(), 'style', '', ', ', '' );
				if ( $styles ) {
					echo '<p class="beers-style"><span class="beers-label">' . esc_html__( 'Style:', 'beer-list' ) . '</span> ' . esc_html( $styles ) . '</p>';
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
add_shortcode( 'beers', 'beers_beers_shortcode' );
