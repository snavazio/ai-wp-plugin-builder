<?php
/**
 * Add meta boxes for slot CPT.
 *
 * @package Bkslot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add datetime meta box to slot editor.
 *
 * @return void
 */
function bkslot_add_datetime_meta_box() {
	add_meta_box(
		'bkslot_datetime_meta_box',
		__( 'Slot Date and Time', 'booking-slots' ),
		'bkslot_render_datetime_meta_box',
		'slot',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'bkslot_add_datetime_meta_box' );

/**
 * Render the datetime meta box.
 *
 * @param WP_Post $post The post object.
 * @return void
 */
function bkslot_render_datetime_meta_box( $post ) {
	$datetime = get_post_meta( $post->ID, '_bkslot_datetime', true );
	$nonce    = wp_create_nonce( 'bkslot_datetime_nonce' );
	?>
	<div class="bkslot-meta-box">
		<label for="bkslot_datetime"><?php esc_html_e( 'Date and Time', 'booking-slots' ); ?></label>
		<input type="datetime-local" id="bkslot_datetime" name="bkslot_datetime" value="<?php echo esc_attr( $datetime ); ?>" class="widefat" />
		<input type="hidden" name="bkslot_datetime_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
	</div>
	<?php
}

/**
 * Save the datetime meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function bkslot_save_datetime_meta_box( $post_id, $post ) {
	if ( ! isset( $_POST['bkslot_datetime_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['bkslot_datetime_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'bkslot_datetime_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['bkslot_datetime'] ) ) {
		return;
	}

	$datetime = sanitize_text_field( wp_unslash( $_POST['bkslot_datetime'] ) );
	update_post_meta( $post_id, '_bkslot_datetime', $datetime );
}
add_action( 'save_post', 'bkslot_save_datetime_meta_box', 10, 2 );
