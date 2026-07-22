<?php
/**
 * Quick Search shortcode handler.
 *
 * @package Qsrch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [quick_search] shortcode.
 *
 * @return string HTML output.
 */
function qsrch_shortcode() {
	ob_start();
	?>
	<div class="qsrch-search-container">
		<input type="text" id="qsrch-search-input" placeholder="<?php esc_attr_e( 'Search titles...', 'quick-search' ); ?>" />
		<div id="qsrch-suggestions"></div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'quick_search', 'qsrch_shortcode' );
