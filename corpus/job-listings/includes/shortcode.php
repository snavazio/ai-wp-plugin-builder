<?php
/**
 * Jobs shortcode handler.
 *
 * @package Jobl
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
function jobl_jobs_shortcode( $atts ) {
	$defaults = array(
		'location'       => '',
		'employment'     => '',
		'salary_min'     => '',
		'salary_max'     => '',
		'posts_per_page' => 10,
	);

	$atts = shortcode_atts( $defaults, $atts, 'jobs' );

	// Sanitize attributes.
	$location       = sanitize_text_field( wp_unslash( $atts['location'] ) );
	$employment     = sanitize_text_field( wp_unslash( $atts['employment'] ) );
	$salary_min     = sanitize_text_field( wp_unslash( $atts['salary_min'] ) );
	$salary_max     = sanitize_text_field( wp_unslash( $atts['salary_max'] ) );
	$posts_per_page = absint( $atts['posts_per_page'] );

	if ( $posts_per_page < 1 ) {
		$posts_per_page = 10;
	}

	$args = array(
		'post_type'      => 'job',
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
	);

	// Build meta query for filtering.
	$meta_query = array();

	if ( ! empty( $location ) ) {
		$meta_query[] = array(
			'key'     => 'jobl_location',
			'value'   => $location,
			'compare' => 'LIKE',
		);
	}

	if ( ! empty( $employment ) ) {
		$meta_query[] = array(
			'key'     => 'jobl_employment_type',
			'value'   => $employment,
			'compare' => 'LIKE',
		);
	}

	if ( ! empty( $meta_query ) ) {
		$args['meta_query'] = $meta_query;
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No jobs found.', 'job-listings' ) . '</p>';
	}

	ob_start();
	?>
	<div class="job-listings" aria-label="<?php esc_attr_e( 'Job listings', 'job-listings' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$location     = get_post_meta( get_the_ID(), 'jobl_location', true );
			$employment   = get_post_meta( get_the_ID(), 'jobl_employment_type', true );
			$salary_range = get_post_meta( get_the_ID(), 'jobl_salary_range', true );
			?>
			<div class="job-listing" itemscope itemtype="https://schema.org/JobPosting">
				<h3 class="job-title" itemprop="title"><?php the_title(); ?></h3>
				<?php if ( $location ) : ?>
					<div class="job-location" itemprop="jobLocation"><?php echo esc_html( $location ); ?></div>
				<?php endif; ?>
				<?php if ( $employment ) : ?>
					<div class="job-employment" itemprop="employmentType"><?php echo esc_html( $employment ); ?></div>
				<?php endif; ?>
				<?php if ( $salary_range ) : ?>
					<div class="job-salary" itemprop="baseSalary"><?php echo esc_html( $salary_range ); ?></div>
				<?php endif; ?>
			</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'jobs', 'jobl_jobs_shortcode' );
