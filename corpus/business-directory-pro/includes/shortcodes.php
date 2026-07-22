<?php
/**
 * Business Listings shortcode handler.
 *
 * @package Bdp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [listings] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function bdp_listings_shortcode( $atts ) {
	$defaults = array(
		'category' => '',
		'region'   => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'listings' );

	// Sanitize attributes.
	$category = sanitize_key( $atts['category'] );
	$region   = sanitize_key( $atts['region'] );

	$args = array(
		'post_type'      => 'bdp_listing',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $category ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	if ( ! empty( $region ) ) {
		if ( empty( $args['tax_query'] ) ) {
			$args['tax_query'] = array();
		}
		$args['tax_query'][] = array(
			'taxonomy' => 'region',
			'field'    => 'slug',
			'terms'    => $region,
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No listings found.', 'business-directory-pro' ) . '</p>';
	}

	ob_start();
	?>
	<div class="bdp-listings" data-category="<?php echo esc_attr( $category ); ?>" data-region="<?php echo esc_attr( $region ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="bdp-listing">
				<h2 class="bdp-listing-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<div class="bdp-listing-phone">
					<?php
					$phone = get_post_meta( get_the_ID(), 'bdp_phone', true );
					if ( $phone ) {
						echo esc_html( $phone );
					}
					?>
				</div>
				<div class="bdp-listing-website">
					<?php
					$website = get_post_meta( get_the_ID(), 'bdp_website', true );
					if ( $website ) {
						echo '<a href="' . esc_url( $website ) . '">' . esc_html( $website ) . '</a>';
					}
					?>
				</div>
				<div class="bdp-listing-categories">
					<?php
					$categories = get_the_term_list( get_the_ID(), 'category', '', ', ', '' );
					if ( $categories ) {
						echo esc_html( $categories );
					}
					?>
				</div>
				<div class="bdp-listing-regions">
					<?php
					$regions = get_the_term_list( get_the_ID(), 'region', '', ', ', '' );
					if ( $regions ) {
						echo esc_html( $regions );
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
add_shortcode( 'listings', 'bdp_listings_shortcode' );
