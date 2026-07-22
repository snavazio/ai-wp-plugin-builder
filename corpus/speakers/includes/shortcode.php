<?php
/**
 * Speakers shortcode handler.
 *
 * @package Spkrs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [speakers] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function spkrs_speakers_shortcode( $atts ) {
	$defaults = array(
		'count' => 10,
	);
	$atts     = shortcode_atts( $defaults, $atts, 'speakers' );

	// Sanitize attributes.
	$count = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'speaker',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No speakers found.', 'speakers' ) . '</p>';
	}

	ob_start();
	?>
	<div class="spkrs-speakers-list" aria-label="<?php esc_attr_e( 'Speakers directory', 'speakers' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$twitter_handle = get_post_meta( get_the_ID(), 'spkrs_twitter_handle', true );
			?>
			<div class="spkrs-speaker" itemscope itemtype="https://schema.org/Person">
				<h3 class="spkrs-speaker-name" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $twitter_handle ) : ?>
					<div class="spkrs-twitter-handle" itemprop="sameAs">
						<a href="https://twitter.com/<?php echo esc_attr( ltrim( $twitter_handle, '@' ) ); ?>" rel="noopener noreferrer" target="_blank">
							<?php echo esc_html( $twitter_handle ); ?>
						</a>
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
add_shortcode( 'speakers', 'spkrs_speakers_shortcode' );
