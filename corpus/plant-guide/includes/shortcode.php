<?php
/**
 * Plant Families shortcode handler.
 *
 * @package Pgui
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [plants] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function pgui_plants_shortcode( $atts ) {
	$defaults = array(
		'family' => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'plants' );

	// Sanitize attributes.
	$family = sanitize_key( $atts['family'] );

	$args = array(
		'post_type'      => 'pgui_plant',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( ! empty( $family ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'pgui_family',
				'field'    => 'slug',
				'terms'    => $family,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No plants found in this family.', 'plant-guide' ) . '</p>';
	}

	ob_start();
	?>
	<div class="pgui-plants-list" data-family="<?php echo esc_attr( $family ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="pgui-plant">
				<h2 class="pgui-plant-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="pgui-plant-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<div class="pgui-plant-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php
				$family_terms = get_the_term_list( get_the_ID(), 'pgui_family', '', ', ', '' );
				if ( $family_terms ) :
					?>
					<div class="pgui-plant-family">
						<?php echo esc_html__( 'Family:', 'plant-guide' ); ?>
						<?php echo esc_html( $family_terms ); ?>
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
add_shortcode( 'plants', 'pgui_plants_shortcode' );
