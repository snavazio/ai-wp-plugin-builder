<?php
/**
 * Courses shortcode handler.
 *
 * @package Crs1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [courses] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function crs1_courses_shortcode( $atts ) {
	$defaults = array(
		'subject' => '',
		'level'   => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'courses' );

	// Sanitize attributes.
	$subject = sanitize_key( $atts['subject'] );
	$level   = sanitize_key( $atts['level'] );

	$args = array(
		'post_type'      => 'crs1_course',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $subject ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'crs1_subject',
				'field'    => 'slug',
				'terms'    => $subject,
			),
		);
	}

	if ( ! empty( $level ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'crs1_level',
				'field'    => 'slug',
				'terms'    => $level,
			),
		);
	}

	// If both subject and level are set, combine them with AND.
	if ( ! empty( $subject ) && ! empty( $level ) ) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'crs1_subject',
				'field'    => 'slug',
				'terms'    => $subject,
			),
			array(
				'taxonomy' => 'crs1_level',
				'field'    => 'slug',
				'terms'    => $level,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No courses found.', 'courses' ) . '</p>';
	}

	ob_start();
	?>
	<div class="crs1-courses-list" data-subject="<?php echo esc_attr( $subject ); ?>" data-level="<?php echo esc_attr( $level ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="crs1-course">
				<h2 class="crs1-course-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="crs1-course-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<div class="crs1-course-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php
				$subjects = get_the_term_list( get_the_ID(), 'crs1_subject', '', ', ', '' );
				$levels   = get_the_term_list( get_the_ID(), 'crs1_level', '', ', ', '' );
				if ( $subjects || $levels ) :
					?>
					<div class="crs1-course-taxonomies">
						<?php if ( $subjects ) : ?>
							<span class="crs1-course-subject"><?php echo esc_html( $subjects ); ?></span>
						<?php endif; ?>
						<?php if ( $levels ) : ?>
							<span class="crs1-course-level"><?php echo esc_html( $levels ); ?></span>
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
add_shortcode( 'courses', 'crs1_courses_shortcode' );
