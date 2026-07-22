<?php
/**
 * AJAX Tabs shortcode handler.
 *
 * @package Ajxt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [tabs] shortcode.
 *
 * @return string HTML output.
 */
function ajxt_shortcode_tabs() {
	$categories = get_categories( array( 'hide_empty' => false ) );

	if ( empty( $categories ) ) {
		return '<p>' . esc_html__( 'No categories found.', 'ajax-tabs' ) . '</p>';
	}

	ob_start();
	?>
	<div class="ajxt-tabs-container">
		<div class="ajxt-tab-buttons">
			<button class="ajxt-tab-btn active" data-tab="">
				<?php esc_html_e( 'All', 'ajax-tabs' ); ?>
			</button>
			<?php foreach ( $categories as $category ) : ?>
				<button class="ajxt-tab-btn" data-tab="<?php echo esc_attr( $category->slug ); ?>">
					<?php echo esc_html( $category->name ); ?>
				</button>
			<?php endforeach; ?>
		</div>
		<div class="ajxt-posts-container"></div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tabs', 'ajxt_shortcode_tabs' );

/**
 * Render a single post item.
 *
 * @return void
 */
function ajxt_render_post_item() {
	?>
	<div class="ajxt-post-item">
		<h3 class="ajxt-post-title">
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php echo esc_html( get_the_title() ); ?>
			</a>
		</h3>
		<div class="ajxt-post-excerpt">
			<?php echo wp_kses_post( get_the_excerpt() ); ?>
		</div>
	</div>
	<?php
}
