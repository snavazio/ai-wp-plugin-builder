<?php
/**
 * Knowledge Base Articles shortcode handler.
 *
 * @package Kbpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [kb_articles] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function kbpl_articles_shortcode( $atts ) {
	$defaults = array(
		'topic' => '',
		'count' => '10',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'kb_articles' );

	// Sanitize attributes.
	$topic = sanitize_key( $atts['topic'] );
	$count = absint( $atts['count'] );

	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'kbpl_article',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $topic ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'kbpl_topic',
				'field'    => 'slug',
				'terms'    => $topic,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No articles found.', 'knowledge-base' ) . '</p>';
	}

	ob_start();
	?>
	<div class="kbpl-articles-list" data-topic="<?php echo esc_attr( $topic ); ?>" data-count="<?php echo esc_attr( (string) $count ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="kbpl-article">
				<h2 class="kbpl-article-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h2>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="kbpl-article-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php endif; ?>
				<div class="kbpl-article-excerpt">
					<?php echo wp_kses_post( get_the_excerpt() ); ?>
				</div>
				<?php if ( $topic ) : ?>
					<div class="kbpl-article-topic">
						<?php echo esc_html__( 'Topic:', 'knowledge-base' ); ?>
						<?php echo esc_html( $topic ); ?>
					</div>
				<?php endif; ?>
			</article>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'kb_articles', 'kbpl_articles_shortcode' );
