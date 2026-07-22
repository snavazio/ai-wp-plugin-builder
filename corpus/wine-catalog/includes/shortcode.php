<?php
/**
 * Wine listing shortcode handler.
 *
 * @package Winec
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [wines] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function winec_wines_shortcode( $atts ) {
	$defaults = array(
		'region'   => '',
		'varietal' => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'wines' );

	// Sanitize attributes.
	$region   = sanitize_key( wp_unslash( $atts['region'] ) );
	$varietal = sanitize_key( wp_unslash( $atts['varietal'] ) );

	$args = array(
		'post_type'      => 'wine',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( ! empty( $region ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'region',
				'field'    => 'slug',
				'terms'    => $region,
			),
		);
	}

	if ( ! empty( $varietal ) ) {
		// If region is set, combine with varietal in tax_query.
		if ( ! empty( $region ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'varietal',
				'field'    => 'slug',
				'terms'    => $varietal,
			);
		} else {
			$args['tax_query'] = array(
				'taxonomy' => 'varietal',
				'field'    => 'slug',
				'terms'    => $varietal,
			);
		}
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No wines found.', 'wine-catalog' ) . '</p>';
	}

	ob_start();
	?>
	<div class="winec-wines-list" data-region="<?php echo esc_attr( $region ); ?>" data-varietal="<?php echo esc_attr( $varietal ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<div class="winec-wine-item">
				<h3 class="winec-wine-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h3>
				<?php
				$vintage = get_post_meta( get_the_ID(), 'winec_vintage', true );
				if ( $vintage ) {
					echo '<p class="winec-wine-vintage"><span class="winec-wine-label">' . esc_html__( 'Vintage:', 'wine-catalog' ) . '</span> ' . esc_html( $vintage ) . '</p>';
				}
				$regions = get_the_term_list( get_the_ID(), 'region', '', ', ', '' );
				if ( $regions ) {
					echo '<p class="winec-wine-region"><span class="winec-wine-label">' . esc_html__( 'Region:', 'wine-catalog' ) . '</span> ' . esc_html( $regions ) . '</p>';
				}
				$varietals = get_the_term_list( get_the_ID(), 'varietal', '', ', ', '' );
				if ( $varietals ) {
					echo '<p class="winec-wine-varietal"><span class="winec-wine-label">' . esc_html__( 'Varietal:', 'wine-catalog' ) . '</span> ' . esc_html( $varietals ) . '</p>';
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
add_shortcode( 'wines', 'winec_wines_shortcode' );
