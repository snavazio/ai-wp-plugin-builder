<?php
/**
 * Work Grid shortcode handler.
 *
 * @package Pskl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [work_grid] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function pskl_work_grid_shortcode( $atts ) {
	$defaults = array(
		'skill' => '',
		'count' => '9',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'work_grid' );

	// Sanitize attributes.
	$skill = sanitize_key( $atts['skill'] );
	$count = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 9;
	}

	$args = array(
		'post_type'      => 'pskl_work',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $skill ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'pskl_skill',
				'field'    => 'slug',
				'terms'    => $skill,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No works found.', 'portfolio-skills' ) . '</p>';
	}

	ob_start();
	?>
	<div class="pskl-work-grid" data-skill="<?php echo esc_attr( $skill ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			pskl_render_work_item();
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'work_grid', 'pskl_work_grid_shortcode' );

/**
 * Render a single work item.
 *
 * @return void
 */
function pskl_render_work_item() {
	$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
	$terms         = get_the_terms( get_the_ID(), 'pskl_skill' );
	$term_names    = array();

	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_names[] = esc_html( $term->name );
		}
	}

	?>
	<div class="pskl-work-item">
		<?php if ( $thumbnail_url ) : ?>
			<div class="pskl-item-thumbnail">
				<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
			</div>
		<?php endif; ?>
		<div class="pskl-item-content">
			<h3 class="pskl-item-title">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php echo esc_html( get_the_title() ); ?>
				</a>
			</h3>
			<?php if ( ! empty( $term_names ) ) : ?>
				<div class="pskl-item-terms">
					<?php echo esc_html( implode( ', ', $term_names ) ); ?>
				</div>
			<?php endif; ?>
			<div class="pskl-item-excerpt">
				<?php echo wp_kses_post( get_the_excerpt() ); ?>
			</div>
		</div>
	</div>
	<?php
}
