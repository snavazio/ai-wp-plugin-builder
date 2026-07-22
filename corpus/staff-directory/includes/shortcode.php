<?php
/**
 * Staff list shortcode handler.
 *
 * @package Staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [staff_list] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function staff_staff_list_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'staff_list' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'staff',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No staff members found.', 'staff-directory' ) . '</p>';
	}

	ob_start();
	?>
	<div class="staff-directory-list" aria-label="<?php esc_attr_e( 'Staff directory', 'staff-directory' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$job_title = get_post_meta( get_the_ID(), 'staff_job_title', true );
			$phone     = get_post_meta( get_the_ID(), 'staff_phone', true );
			?>
			<div class="staff-member" itemscope itemtype="https://schema.org/Person">
				<h3 class="staff-member-name" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $job_title ) : ?>
					<div class="staff-member-job-title" itemprop="jobTitle"><?php echo esc_html( $job_title ); ?></div>
				<?php endif; ?>
				<?php if ( $phone ) : ?>
					<div class="staff-member-phone" itemprop="telephone">
						<a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
					</div>
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
add_shortcode( 'staff_list', 'staff_staff_list_shortcode' );
