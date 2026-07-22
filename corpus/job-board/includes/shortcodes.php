<?php
/**
 * Job Board shortcode handler.
 *
 * @package Jbrd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [jobs] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function jbrd_jobs_shortcode( $atts ) {
	$defaults = array(
		'category' => '',
		'count'    => '10',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'jobs' );

	// Sanitize attributes.
	$category = sanitize_key( $atts['category'] );
	$count    = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'jbrd_job',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $category ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'jbrd_job_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No jobs found.', 'job-board' ) . '</p>';
	}

	ob_start();
	?>
	<div class="jbrd-jobs" data-category="<?php echo esc_attr( $category ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="jbrd-job">
				<h2 class="jbrd-job-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="jbrd-job-location">
					<?php
					$location = get_post_meta( get_the_ID(), 'jbrd_location', true );
					if ( $location ) {
						echo esc_html( $location );
					}
					?>
				</div>
				<div class="jbrd-job-salary">
					<?php
					$salary = get_post_meta( get_the_ID(), 'jbrd_salary', true );
					if ( $salary ) {
						echo esc_html( $salary );
					}
					?>
				</div>
				<div class="jbrd-job-categories">
					<?php
					$categories = get_the_term_list( get_the_ID(), 'jbrd_job_category', '', ', ', '' );
					if ( $categories ) {
						echo esc_html( $categories );
					}
					?>
				</div>
			</article>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'jobs', 'jbrd_jobs_shortcode' );
