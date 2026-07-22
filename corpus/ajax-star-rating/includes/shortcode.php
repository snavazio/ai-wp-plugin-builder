<?php
/**
 * Star Rating shortcode handler.
 *
 * @package Arsr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [star_rating] shortcode.
 *
 * @return string HTML output.
 */
function arsr_shortcode_star_rating() {
	$post_id = get_the_ID();
	$total   = get_post_meta( $post_id, 'arsr_total', true );
	$count   = get_post_meta( $post_id, 'arsr_count', true );

	if ( ! is_numeric( $total ) || ! is_numeric( $count ) || 0 === $count ) {
		$total = 0;
		$count = 0;
	}

	$average = $count ? $total / $count : 0;
	$stars   = floor( $average );
	$half    = $average - $stars >= 0.5;

	ob_start();
	?>
	<div class="arsr-rating-container" data-post-id="<?php echo esc_attr( (string) $post_id ); ?>">
		<div class="arsr-stars">
			<?php for ( $i = 1; 5 >= $i; $i++ ) : ?>
				<span class="arsr-star" data-rating="<?php echo esc_attr( (string) $i ); ?>">
					<?php echo esc_attr( $stars >= $i ? '★' : '☆' ); ?>
				</span>
			<?php endfor; ?>
		</div>
		<div class="arsr-rating-value">
			<?php
			printf(
				/* translators: %s: average rating */
				esc_html__( 'Average: %s', 'ajax-star-rating' ),
				esc_html( number_format( $average, 1 ) )
			);
			?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'star_rating', 'arsr_shortcode_star_rating' );
