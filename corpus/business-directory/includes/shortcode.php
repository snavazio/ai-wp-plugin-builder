<?php
/**
 * Business directory shortcode handler.
 *
 * @package Bdir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [directory] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function bdir_directory_shortcode( $atts ) {
	$defaults = array(
		'category' => '',
		'region'   => '',
		'count'    => '10',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'directory' );

	// Sanitize attributes.
	$category = sanitize_key( $atts['category'] );
	$region   = sanitize_key( $atts['region'] );
	$count    = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'business',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $category ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'bdir_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	if ( ! empty( $region ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'bdir_region',
				'field'    => 'slug',
				'terms'    => $region,
			),
		);
	}

	// If both category and region are set, combine them with AND.
	if ( ! empty( $category ) && ! empty( $region ) ) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'bdir_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
			array(
				'taxonomy' => 'bdir_region',
				'field'    => 'slug',
				'terms'    => $region,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No businesses found.', 'business-directory' ) . '</p>';
	}

	ob_start();
	?>
	<div class="bdir-directory" data-category="<?php echo esc_attr( $category ); ?>" data-region="<?php echo esc_attr( $region ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="bdir-business">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="bdir-business-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<h2 class="bdir-business-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="bdir-business-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php
				$categories = get_the_term_list( get_the_ID(), 'bdir_category', '', ', ', '' );
				$regions    = get_the_term_list( get_the_ID(), 'bdir_region', '', ', ', '' );
				if ( $categories || $regions ) :
					?>
					<div class="bdir-business-taxonomies">
						<?php if ( $categories ) : ?>
							<span class="bdir-business-category"><?php echo esc_html( $categories ); ?></span>
						<?php endif; ?>
						<?php if ( $regions ) : ?>
							<span class="bdir-business-region"><?php echo esc_html( $regions ); ?></span>
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
add_shortcode( 'directory', 'bdir_directory_shortcode' );
