<?php
/**
 * Reaction Buttons shortcode handler.
 *
 * @package Reac
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [reactions] shortcode.
 *
 * @return string HTML output.
 */
function reac_shortcode_reactions() {
	$reactions = array(
		'like'  => '👍',
		'love'  => '❤️',
		'haha'  => '😂',
		'wow'   => '😮',
		'sad'   => '😢',
		'angry' => '😡',
	);

	ob_start();
	?>
	<div class="reac-reactions">
		<?php foreach ( $reactions as $key => $emoji ) : ?>
			<button class="reac-reaction-button" data-reaction="<?php echo esc_attr( $key ); ?>">
				<?php echo esc_html( $emoji ); ?>
			</button>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'reactions', 'reac_shortcode_reactions' );
