<?php
/**
 * Employees shortcode handler.
 *
 * @package Empc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [employees] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function empc_employees_shortcode( $atts ) {
	$defaults = array(
		'count' => 10,
	);

	$atts = shortcode_atts( $defaults, $atts, 'employees' );

	// Sanitize attributes.
	$count = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'employee',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No employees found.', 'employees' ) . '</p>';
	}

	ob_start();
	?>
	<div class="empc-employees-list" aria-label="<?php esc_attr_e( 'Employees directory', 'employees' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$department = get_post_meta( get_the_ID(), 'empc_department', true );
			$email      = get_post_meta( get_the_ID(), 'empc_email', true );
			?>
			<div class="empc-employee" itemscope itemtype="https://schema.org/Person">
				<h3 class="empc-employee-name" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $department ) : ?>
					<div class="empc-employee-department" itemprop="department"><?php echo esc_html( $department ); ?></div>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<div class="empc-employee-email" itemprop="email">
						<a href="mailto:<?php echo esc_attr( $email ); ?>" rel="nofollow"><?php echo esc_html( $email ); ?></a>
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
add_shortcode( 'employees', 'empc_employees_shortcode' );
