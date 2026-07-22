<?php
/**
 * Case Studies shortcode handler.
 *
 * @package Cstuds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [case_studies] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function cstuds_case_studies_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'case_studies' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'case-study',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No case studies found.', 'case-studies' ) . '</p>';
	}

	ob_start();
	?>
	<div class="cstuds-case-studies" aria-label="<?php esc_attr_e( 'Case studies list', 'case-studies' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$client  = get_post_meta( get_the_ID(), 'cstuds_client', true );
			$outcome = get_post_meta( get_the_ID(), 'cstuds_outcome', true );
			?>
			<div class="cstuds-case-study" itemscope itemtype="https://schema.org/CreativeWork">
				<h3 class="cstuds-case-study-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $client ) : ?>
					<div class="cstuds-client" itemprop="author"><?php echo esc_html( $client ); ?></div>
				<?php endif; ?>
				<?php if ( $outcome ) : ?>
					<div class="cstuds-outcome" itemprop="description"><?php echo wp_kses_post( $outcome ); ?></div>
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
add_shortcode( 'case_studies', 'cstuds_case_studies_shortcode' );
