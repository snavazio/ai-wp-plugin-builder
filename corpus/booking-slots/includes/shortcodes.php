<?php
/**
 * Slot shortcode handler.
 *
 * @package Bkslot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [slots] shortcode.
 *
 * @return string HTML output.
 */
function bkslot_slots_shortcode() {
	$slots = get_posts(
		array(
			'post_type'      => 'slot',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_bkslot_booked',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);

	if ( ! $slots ) {
		return '<p>' . esc_html__( 'No available slots found.', 'booking-slots' ) . '</p>';
	}

	ob_start();
	?>
	<div class="bkslot-slots-list">
		<?php foreach ( $slots as $slot ) : ?>
			<div class="bkslot-slot" data-slot-id="<?php echo esc_attr( (string) $slot->ID ); ?>">
				<h3><?php echo esc_html( get_the_title( $slot->ID ) ); ?></h3>
				<p><?php echo esc_html( get_post_meta( $slot->ID, '_bkslot_datetime', true ) ); ?></p>
				<button class="bkslot-book-button" data-slot-id="<?php echo esc_attr( (string) $slot->ID ); ?>">
					<?php esc_html_e( 'Book Slot', 'booking-slots' ); ?>
				</button>
				<div class="bkslot-response"></div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'slots', 'bkslot_slots_shortcode' );
