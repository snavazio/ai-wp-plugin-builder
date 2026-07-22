<?php
/**
 * Press Releases list shortcode handler.
 *
 * @package Prcs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [press_releases] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function prcs_press_releases_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'press_releases' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'press_release',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No press releases found.', 'press-releases' ) . '</p>';
	}

	ob_start();
	?>
	<div class="prcs-press-releases" aria-label="<?php esc_attr_e( 'Press Releases', 'press-releases' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$release_date = get_post_meta( get_the_ID(), 'prcs_release_date', true );
			?>
			<div class="prcs-press-release" itemscope itemtype="https://schema.org/NewsArticle">
				<h3 class="prcs-press-release-title" itemprop="headline"><?php the_title(); ?></h3>
				<?php if ( $release_date ) : ?>
					<div class="prcs-release-date" itemprop="datePublished">
						<?php echo esc_html( $release_date ); ?>
					</div>
				<?php endif; ?>
				<div class="prcs-press-release-content" itemprop="description">
					<?php the_excerpt(); ?>
				</div>
			</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'press_releases', 'prcs_press_releases_shortcode' );
