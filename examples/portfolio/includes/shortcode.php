<?php
/**
 * Portfolio shortcode handler.
 *
 * @package Prtf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [portfolio] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function prtf_portfolio_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'type'  => '',
			'count' => '9',
		),
		$atts,
		'portfolio'
	);

	$type  = sanitize_key( $atts['type'] );
	$count = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 9;
	}

	$args = array(
		'post_type'      => 'prtf_portfolio',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $type ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'prtf_project_type',
				'field'    => 'slug',
				'terms'    => $type,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No portfolio projects found.', 'portfolio' ) . '</p>';
	}

	ob_start();
	?>
	<div class="prtf-portfolio-grid" data-type="<?php echo esc_attr( $type ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>" data-page="1">
		<div class="prtf-grid-items">
			<?php
			while ( $query->have_posts() ) {
				$query->the_post();
				prtf_render_portfolio_item();
			}
			wp_reset_postdata();
			?>
		</div>
		<?php if ( $query->max_num_pages > 1 ) : ?>
			<div class="prtf-load-more-wrapper">
				<button class="prtf-load-more-btn" data-max-pages="<?php echo esc_attr( (string) $query->max_num_pages ); ?>">
					<?php esc_html_e( 'Load More', 'portfolio' ); ?>
				</button>
			</div>
		<?php endif; ?>
	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'portfolio', 'prtf_portfolio_shortcode' );

/**
 * Render a single portfolio item.
 *
 * @return void
 */
function prtf_render_portfolio_item() {
	$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
	$terms         = get_the_terms( get_the_ID(), 'prtf_project_type' );
	$term_names    = array();

	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_names[] = esc_html( $term->name );
		}
	}

	?>
	<div class="prtf-portfolio-item">
		<?php if ( $thumbnail_url ) : ?>
			<div class="prtf-item-thumbnail">
				<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
			</div>
		<?php endif; ?>
		<div class="prtf-item-content">
			<h3 class="prtf-item-title">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php echo esc_html( get_the_title() ); ?>
				</a>
			</h3>
			<?php if ( ! empty( $term_names ) ) : ?>
				<div class="prtf-item-terms">
					<?php echo esc_html( implode( ', ', $term_names ) ); ?>
				</div>
			<?php endif; ?>
			<div class="prtf-item-excerpt">
				<?php echo wp_kses_post( get_the_excerpt() ); ?>
			</div>
		</div>
	</div>
	<?php
}
