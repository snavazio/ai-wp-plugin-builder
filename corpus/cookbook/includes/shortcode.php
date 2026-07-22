<?php
/**
 * Recipe directory shortcode handler.
 *
 * @package Cook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [recipes] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function cook_recipes_shortcode( $atts ) {
	$defaults = array(
		'course'  => '',
		'cuisine' => '',
		'count'   => '10',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'recipes' );

	// Sanitize attributes.
	$course  = sanitize_key( $atts['course'] );
	$cuisine = sanitize_key( $atts['cuisine'] );
	$count   = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'recipe',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $course ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'cook_course',
				'field'    => 'slug',
				'terms'    => $course,
			),
		);
	}

	if ( ! empty( $cuisine ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'cook_cuisine',
				'field'    => 'slug',
				'terms'    => $cuisine,
			),
		);
	}

	// If both course and cuisine are set, combine them with AND.
	if ( ! empty( $course ) && ! empty( $cuisine ) ) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'cook_course',
				'field'    => 'slug',
				'terms'    => $course,
			),
			array(
				'taxonomy' => 'cook_cuisine',
				'field'    => 'slug',
				'terms'    => $cuisine,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No recipes found.', 'cookbook' ) . '</p>';
	}

	ob_start();
	?>
	<div class="cook-recipes" data-course="<?php echo esc_attr( $course ); ?>" data-cuisine="<?php echo esc_attr( $cuisine ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="cook-recipe">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="cook-recipe-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<h2 class="cook-recipe-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="cook-recipe-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php
				$courses  = get_the_term_list( get_the_ID(), 'cook_course', '', ', ', '' );
				$cuisines = get_the_term_list( get_the_ID(), 'cook_cuisine', '', ', ', '' );
				if ( $courses || $cuisines ) :
					?>
					<div class="cook-recipe-taxonomies">
						<?php if ( $courses ) : ?>
							<span class="cook-recipe-course"><?php echo esc_html( $courses ); ?></span>
						<?php endif; ?>
						<?php if ( $cuisines ) : ?>
							<span class="cook-recipe-cuisine"><?php echo esc_html( $cuisines ); ?></span>
						<?php endif; ?>
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
add_shortcode( 'recipes', 'cook_recipes_shortcode' );
