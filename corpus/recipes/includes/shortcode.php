<?php
/**
 * Recipe card shortcode handler.
 *
 * @package Rcpr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [recipe_card] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function rcpr_recipe_card_shortcode( $atts ) {
	$defaults = array(
		'id' => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'recipe_card' );

	// Sanitize attributes.
	$post_id = ! empty( $atts['id'] ) ? absint( $atts['id'] ) : get_the_ID();

	if ( ! $post_id || ! get_post( $post_id ) ) {
		return '<p>' . esc_html__( 'Recipe not found.', 'recipes' ) . '</p>';
	}

	$prep_time = get_post_meta( $post_id, 'rcpr_prep_time', true );
	$servings  = get_post_meta( $post_id, 'rcpr_servings', true );

	ob_start();
	?>
	<div class="rcpr-recipe-card" itemscope itemtype="https://schema.org/Recipe">
		<h2 class="rcpr-recipe-title" itemprop="name"><?php the_title( '<span itemprop="name">', '</span>' ); ?></h2>
		<?php if ( $prep_time ) : ?>
			<div class="rcpr-recipe-meta" itemprop="prepTime">
				<?php echo esc_html( $prep_time ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $servings ) : ?>
			<div class="rcpr-recipe-meta" itemprop="recipeYield">
				<?php // translators: %d is the number of servings. ?>
				<?php echo esc_html( sprintf( _n( 'Serves %d', 'Serves %d', $servings, 'recipes' ), $servings ) ); ?>
			</div>
		<?php endif; ?>
		<div class="rcpr-recipe-content" itemprop="description">
			<?php the_content(); ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'recipe_card', 'rcpr_recipe_card_shortcode' );
