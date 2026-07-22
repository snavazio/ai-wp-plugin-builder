<?php
/**
 * AJAX Post Filter shortcode handler.
 *
 * @package Apf1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [post_filter] shortcode.
 *
 * @return string HTML output.
 */
function apf1_shortcode_post_filter() {
	$categories = get_categories( array( 'hide_empty' => false ) );

	if ( empty( $categories ) ) {
		return '<p>' . esc_html__( 'No categories found.', 'ajax-post-filter' ) . '</p>';
	}

	ob_start();
	?>
	<div class="apf1-post-filter-container">
		<div class="apf1-category-buttons">
			<button class="apf1-category-btn active" data-category="">
				<?php esc_html_e( 'All', 'ajax-post-filter' ); ?>
			</button>
			<?php foreach ( $categories as $category ) : ?>
				<button class="apf1-category-btn" data-category="<?php echo esc_attr( $category->slug ); ?>">
					<?php echo esc_html( $category->name ); ?>
				</button>
			<?php endforeach; ?>
		</div>
		<div class="apf1-posts-container"></div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'post_filter', 'apf1_shortcode_post_filter' );

/**
 * Render a single post item.
 *
 * @return void
 */
function apf1_render_post_item() {
	?>
	<div class="apf1-post-item">
		<h3 class="apf1-post-title">
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php echo esc_html( get_the_title() ); ?>
			</a>
		</h3>
		<div class="apf1-post-excerpt">
			<?php echo wp_kses_post( get_the_excerpt() ); ?>
		</div>
	</div>
	<?php
}
