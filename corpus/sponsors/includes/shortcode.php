<?php
/**
 * Sponsors shortcode handler.
 *
 * @package Spns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [sponsors] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function spns_sponsors_shortcode( $atts ) {
	$defaults = array(
		'tier'    => '',
		'count'   => -1,
		'order'   => 'asc',
		'orderby' => 'title',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'sponsors' );

	// Sanitize attributes.
	$tier    = sanitize_key( wp_unslash( $atts['tier'] ) );
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( wp_unslash( $atts['order'] ) );
	$orderby = sanitize_key( wp_unslash( $atts['orderby'] ) );

	// Validate order.
	if ( ! in_array( $order, array( 'asc', 'desc' ), true ) ) {
		$order = 'asc';
	}

	// Validate orderby.
	if ( ! in_array( $orderby, array( 'title', 'date', 'modified' ), true ) ) {
		$orderby = 'title';
	}

	// Handle count=0 as all terms.
	if ( 0 === $count ) {
		$count = -1;
	}

	$args = array(
		'post_type'      => 'spns_sponsor',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	if ( ! empty( $tier ) ) {
		$args['meta_query'] = array(
			array(
				'key'     => 'spns_tier',
				'value'   => $tier,
				'compare' => '=',
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No sponsors found.', 'sponsors' ) . '</p>';
	}

	ob_start();
	?>
	<div class="spns-sponsors" aria-label="<?php esc_attr_e( 'Sponsors list', 'sponsors' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$tier = get_post_meta( get_the_ID(), 'spns_tier', true );
			?>
			<div class="spns-sponsor" itemscope itemtype="https://schema.org/Organization">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="spns-sponsor-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<h3 class="spns-sponsor-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( ! empty( $tier ) ) : ?>
					<div class="spns-sponsor-tier" itemprop="sponsorTier"><?php echo esc_html( $tier ); ?></div>
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
add_shortcode( 'sponsors', 'spns_sponsors_shortcode' );
