<?php
/**
 * Random Quote shortcode handler.
 *
 * @package Qcl1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [random_quote] shortcode.
 *
 * @return string HTML output.
 */
function qcl1_random_quote_shortcode() {
	$args = array(
		'post_type'      => 'quote',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'orderby'        => 'rand',
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No quotes found.', 'quotes-collection' ) . '</p>';
	}

	ob_start();
	?>
	<div class="qcl1-random-quote" aria-label="<?php esc_attr_e( 'Random quote', 'quotes-collection' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<blockquote class="qcl1-quote" itemprop="text">
				<?php the_content(); ?>
				<?php if ( get_the_author_meta( 'display_name' ) ) : ?>
					<footer class="qcl1-quote-author">
						<?php echo esc_html__( '— ', 'quotes-collection' ) . esc_html( get_the_author_meta( 'display_name' ) ); ?>
					</footer>
				<?php endif; ?>
			</blockquote>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'random_quote', 'qcl1_random_quote_shortcode' );
