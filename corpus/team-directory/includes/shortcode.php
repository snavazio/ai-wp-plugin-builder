<?php
/**
 * Team Directory shortcode handler.
 *
 * @package Tdir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [tdir_team] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function tdir_team_shortcode( $atts ) {
	$defaults = array(
		'department' => '',
		'count'      => '10',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'tdir_team' );

	// Sanitize attributes.
	$department = sanitize_key( $atts['department'] );
	$count      = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'tdir_team_member',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $department ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'tdir_department',
				'field'    => 'slug',
				'terms'    => $department,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No team members found.', 'team-directory' ) . '</p>';
	}

	ob_start();
	?>
	<div class="tdir-team-directory" data-department="<?php echo esc_attr( $department ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="tdir-team-member">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="tdir-team-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<h2 class="tdir-team-name">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="tdir-team-role">
					<?php
					$role = get_post_meta( get_the_ID(), 'tdir_role', true );
					if ( $role ) {
						echo esc_html( $role );
					}
					?>
				</div>
				<div class="tdir-team-email">
					<?php
					$email = get_post_meta( get_the_ID(), 'tdir_email', true );
					if ( $email ) {
						echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
					}
					?>
				</div>
				<?php
				$departments = get_the_term_list( get_the_ID(), 'tdir_department', '', ', ', '' );
				if ( $departments ) :
					?>
					<div class="tdir-team-departments">
						<?php echo esc_html( $departments ); ?>
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
add_shortcode( 'tdir_team', 'tdir_team_shortcode' );
