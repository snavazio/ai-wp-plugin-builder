<?php
/**
 * Glossary Terms shortcode handler.
 *
 * @package Gterm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [glossary] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function gterm_glossary_shortcode( $atts ) {
	$defaults = array(
		'count'   => -1,
		'order'   => 'asc',
		'orderby' => 'title',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'glossary' );

	// Sanitize attributes.
	$count   = absint( $atts['count'] );
	$order   = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );

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
		'post_type'      => 'gterm_glossary_term',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No glossary terms found.', 'glossary-terms' ) . '</p>';
	}

	ob_start();
	?>
	<div class="gterm-glossary" aria-label="<?php esc_attr_e( 'Glossary terms list', 'glossary-terms' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<div class="gterm-glossary-term" itemscope itemtype="https://schema.org/Definition">
				<h3 class="gterm-glossary-term-title" itemprop="name"><?php the_title(); ?></h3>
				<div class="gterm-glossary-term-definition" itemprop="description"><?php the_content(); ?></div>
			</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'glossary', 'gterm_glossary_shortcode' );
