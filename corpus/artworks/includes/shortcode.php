<?php
/**
 * Artworks shortcode handler.
 *
 * @package Artw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [artw_artworks] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function artw_artworks_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'artw_artworks' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'artw_artwork',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No artworks found.', 'artworks' ) . '</p>';
	}

	ob_start();
	?>
	<div class="artw-artworks" aria-label="<?php esc_attr_e( 'Artworks list', 'artworks' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$artist = get_post_meta( get_the_ID(), 'artw_artist', true );
			$medium = get_post_meta( get_the_ID(), 'artw_medium', true );
			?>
			<div class="artw-artwork" itemscope itemtype="https://schema.org/Artwork">
				<h3 class="artw-artwork-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $artist ) : ?>
					<div class="artw-artist" itemprop="artist"><?php echo esc_html( $artist ); ?></div>
				<?php endif; ?>
				<?php if ( $medium ) : ?>
					<div class="artw-medium" itemprop="material"><?php echo esc_html( $medium ); ?></div>
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
add_shortcode( 'artw_artworks', 'artw_artworks_shortcode' );
