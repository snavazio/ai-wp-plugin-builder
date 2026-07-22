<?php
/**
 * Menu shortcode handler.
 *
 * @package Rmenu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [menu] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function rmenu_menu_shortcode( $atts ) {
	$defaults = array(
		'category' => '',
	);

	$atts = shortcode_atts( $defaults, $atts, 'menu' );

	// Sanitize attributes.
	$category = sanitize_text_field( wp_unslash( $atts['category'] ) );

	$args = array(
		'post_type'      => 'menu_item',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	if ( ! empty( $category ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'menu_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No menu items found.', 'restaurant-menu' ) . '</p>';
	}

	ob_start();
	?>
	<div class="rmenu-menu" aria-label="<?php esc_attr_e( 'Restaurant menu items', 'restaurant-menu' ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$price   = get_post_meta( get_the_ID(), 'rmenu_price', true );
			$dietary = get_post_meta( get_the_ID(), 'rmenu_dietary_notes', true );
			?>
			<div class="rmenu-menu-item" itemscope itemtype="https://schema.org/FoodItem">
				<h3 class="rmenu-menu-item-title" itemprop="name"><?php the_title(); ?></h3>
				<?php if ( $price ) : ?>
					<div class="rmenu-menu-item-price" itemprop="price"><?php echo esc_html( $price ); ?></div>
				<?php endif; ?>
				<?php if ( $dietary ) : ?>
					<div class="rmenu-menu-item-dietary" itemprop="description"><?php echo esc_html( $dietary ); ?></div>
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
add_shortcode( 'menu', 'rmenu_menu_shortcode' );
