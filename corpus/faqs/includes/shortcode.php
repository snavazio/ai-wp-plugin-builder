<?php
/**
 * FAQs shortcode handler.
 *
 * @package Faqs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [faqs] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function faqs_shortcode( $atts ) {
	$defaults = array(
		'count'   => 10,
		'order'   => 'date',
		'orderby' => 'date',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'faqs' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'faqs_faq',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No FAQs found.', 'faqs' ) . '</p>';
	}

	ob_start();
	?>
	<div class="faqs-accordion" aria-label="<?php esc_attr_e( 'FAQs accordion', 'faqs' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<div class="faq-item">
				<button class="faq-question" aria-expanded="false" aria-controls="faq-<?php the_ID(); ?>">
					<?php the_title(); ?>
				</button>
				<div id="faq-<?php the_ID(); ?>" class="faq-answer" hidden>
					<?php the_content(); ?>
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
add_shortcode( 'faqs', 'faqs_shortcode' );
