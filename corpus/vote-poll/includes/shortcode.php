<?php
/**
 * Vote Poll shortcode handler.
 *
 * @package Vtpol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [poll] shortcode.
 *
 * @return string HTML output.
 */
function vtpol_shortcode_poll() {
	// Generate unique poll ID.
	$poll_id = 'vtpol_poll_' . substr( wp_generate_password( 10, false, false ), 0, 10 );

	// Initialize counts if not exists.
	$counts = get_option(
		$poll_id,
		array(
			'yes' => 0,
			'no'  => 0,
		)
	);

	ob_start();
	?>
	<div class="vtpol-poll" data-poll-id="<?php echo esc_attr( $poll_id ); ?>">
		<div class="vtpol-vote-counts">
			<span class="vtpol-vote-count vtpol-vote-yes"><?php echo esc_html( $counts['yes'] ); ?></span>
			<span class="vtpol-vote-count vtpol-vote-no"><?php echo esc_html( $counts['no'] ); ?></span>
		</div>
		<div class="vtpol-vote-buttons">
			<button class="vtpol-vote-button vtpol-vote-yes" data-vote="yes">
				<?php esc_html_e( 'Yes', 'vote-poll' ); ?>
			</button>
			<button class="vtpol-vote-button vtpol-vote-no" data-vote="no">
				<?php esc_html_e( 'No', 'vote-poll' ); ?>
			</button>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'poll', 'vtpol_shortcode_poll' );
