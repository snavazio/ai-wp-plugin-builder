<?php
/**
 * Load More Posts shortcode handler.
 *
 * @package Lmpa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [load_more] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function lmpa_shortcode_load_more( $atts ) {
	$atts = shortcode_atts(
		array(
			'count' => '9',
		),
		$atts,
		'load_more'
	);

	$count = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 9;
	}

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No posts found.', 'load-more-posts' ) . '</p>';
	}

	ob_start();
	?>
	<div class="lmpa-load-more-container" data-count="<?php echo esc_attr( (string) $count ); ?>" data-page="1">
		<div class="lmpa-posts-grid">
			<?php
			while ( $query->have_posts() ) {
				$query->the_post();
				lmpa_render_post_item();
			}
			wp_reset_postdata();
			?>
		</div>
		<?php if ( $query->max_num_pages > 1 ) : ?>
			<div class="lmpa-load-more-wrapper">
				<button class="lmpa-load-more-btn" data-max-pages="<?php echo esc_attr( (string) $query->max_num_pages ); ?>">
					<?php esc_html_e( 'Load More', 'load-more-posts' ); ?>
				</button>
			</div>
		<?php endif; ?>
	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'load_more', 'lmpa_shortcode_load_more' );

/**
 * Render a single post item.
 *
 * @return void
 */
function lmpa_render_post_item() {
	?>
	<div class="lmpa-post-item">
		<h3 class="lmpa-post-title">
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php echo esc_html( get_the_title() ); ?>
			</a>
		</h3>
		<div class="lmpa-post-excerpt">
			<?php echo wp_kses_post( get_the_excerpt() ); ?>
		</div>
	</div>
	<?php
}
