<?php
/**
 * Downloads list shortcode handler.
 *
 * @package Dlm1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [downloads] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function dlm1_downloads_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'downloads' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'download',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No downloads found.', 'downloads' ) . '</p>';
	}

	ob_start();
	?>
	<div class="dlm1-downloads" aria-label="<?php esc_attr_e( 'Downloads', 'downloads' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$file_url = get_post_meta( get_the_ID(), 'dlm1_file_url', true );
			$count    = get_post_meta( get_the_ID(), 'dlm1_download_count', true );
			?>
			<div class="dlm1-download" itemscope itemtype="https://schema.org/SoftwareApplication">
				<h3 class="dlm1-download-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $file_url ) : ?>
					<a href="<?php echo esc_url( $file_url ); ?>" class="dlm1-download-link" itemprop="downloadUrl">
						<?php esc_html_e( 'Download', 'downloads' ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $count ) : ?>
					<div class="dlm1-download-count" itemprop="interactionCount">
						<?php // translators: %d is the download count. ?>
						<?php echo esc_html( sprintf( _n( 'Downloaded %d time', 'Downloaded %d times', $count, 'downloads' ), $count ) ); ?>
					</div>
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
add_shortcode( 'downloads', 'dlm1_downloads_shortcode' );
