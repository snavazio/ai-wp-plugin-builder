<?php
/**
 * Admin page for Custom Head Markup plugin.
 *
 * @package Chmp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Custom Head Markup settings page.
 *
 * @return void
 */
function chmp_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'custom-head-markup' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['chmp_save'] ) && isset( $_POST['chmp_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['chmp_nonce'] ) ), 'chmp_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'custom-head-markup' ) );
		}

		// Sanitize input.
		$markup = '';
		if ( isset( $_POST['chmp_head_markup'] ) ) {
			$markup = wp_kses( wp_unslash( $_POST['chmp_head_markup'] ), chmp_get_allowed_html() );
		}

		// Update option.
		update_option( 'chmp_head_markup', $markup );
	}

	// Get current markup.
	$markup = get_option( 'chmp_head_markup', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Custom Head Markup', 'custom-head-markup' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'chmp_save', 'chmp_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="chmp_head_markup"><?php esc_html_e( 'Custom Head Markup', 'custom-head-markup' ); ?></label>
					</th>
					<td>
						<textarea name="chmp_head_markup" id="chmp_head_markup" rows="8" class="large-text"><?php echo esc_textarea( $markup ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter HTML to output in the <head> section (e.g., meta tags, analytics scripts). Allowed tags: a, abbr, address, area, article, aside, audio, b, base, bdi, bdo, blockquote, body, br, button, canvas, caption, cite, code, col, colgroup, data, datalist, dd, del, details, dfn, div, dl, dt, em, embed, fieldset, figcaption, figure, footer, form, h1, h2, h3, h4, h5, h6, head, header, hr, html, i, iframe, img, input, ins, kbd, label, legend, li, link, main, map, mark, menu, meta, meter, nav, noscript, object, ol, optgroup, option, output, p, param, pre, progress, q, rp, rt, ruby, s, samp, script, section, select, small, source, span, strong, style, sub, summary, sup, table, tbody, td, tfoot, th, thead, time, title, tr, track, u, ul, var, video, wbr.', 'custom-head-markup' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Register the Custom Head Markup settings page.
 *
 * @return void
 */
function chmp_add_settings_page() {
	add_options_page(
		__( 'Custom Head Markup', 'custom-head-markup' ),
		__( 'Custom Head Markup', 'custom-head-markup' ),
		'manage_options',
		'chmp-settings',
		'chmp_admin_page_callback'
	);
}
add_action( 'admin_menu', 'chmp_add_settings_page' );

/**
 * Get allowed HTML tags and attributes for head markup.
 *
 * @return array Allowed HTML tags and attributes.
 */
function chmp_get_allowed_html() {
	return array(
		'a'          => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
			'rel'    => array(),
		),
		'abbr'       => array(),
		'address'    => array(),
		'area'       => array(
			'href'   => array(),
			'alt'    => array(),
			'title'  => array(),
			'rel'    => array(),
			'coords' => array(),
			'shape'  => array(),
		),
		'article'    => array(),
		'aside'      => array(),
		'audio'      => array(
			'controls' => array(),
			'preload'  => array(),
		),
		'b'          => array(),
		'base'       => array(
			'href' => array(),
		),
		'bdi'        => array(),
		'bdo'        => array(),
		'blockquote' => array(
			'cite' => array(),
		),
		'body'       => array(),
		'br'         => array(),
		'button'     => array(
			'type' => array(),
		),
		'canvas'     => array(
			'width'  => array(),
			'height' => array(),
		),
		'caption'    => array(),
		'cite'       => array(),
		'code'       => array(),
		'col'        => array(
			'span' => array(),
		),
		'colgroup'   => array(
			'span' => array(),
		),
		'data'       => array(
			'value' => array(),
		),
		'datalist'   => array(),
		'dd'         => array(),
		'del'        => array(
			'datetime' => array(),
		),
		'details'    => array(
			'start' => array(),
		),
		'dfn'        => array(),
		'div'        => array(),
		'dl'         => array(),
		'dt'         => array(),
		'em'         => array(),
		'embed'      => array(
			'src'    => array(),
			'type'   => array(),
			'width'  => array(),
			'height' => array(),
		),
		'fieldset'   => array(),
		'figcaption' => array(),
		'figure'     => array(),
		'footer'     => array(),
		'form'       => array(
			'action' => array(),
			'method' => array(),
			'target' => array(),
		),
		'h1'         => array(),
		'h2'         => array(),
		'h3'         => array(),
		'h4'         => array(),
		'h5'         => array(),
		'h6'         => array(),
		'head'       => array(),
		'header'     => array(),
		'hr'         => array(),
		'html'       => array(),
		'i'          => array(),
		'iframe'     => array(
			'src'          => array(),
			'width'        => array(),
			'height'       => array(),
			'frameborder'  => array(),
			'scrolling'    => array(),
			'marginwidth'  => array(),
			'marginheight' => array(),
			'style'        => array(),
			'class'        => array(),
			'id'           => array(),
		),
		'img'        => array(
			'src'    => array(),
			'alt'    => array(),
			'width'  => array(),
			'height' => array(),
			'style'  => array(),
			'class'  => array(),
			'id'     => array(),
		),
		'input'      => array(
			'type'        => array(),
			'name'        => array(),
			'value'       => array(),
			'placeholder' => array(),
			'style'       => array(),
			'class'       => array(),
			'id'          => array(),
		),
		'ins'        => array(
			'datetime' => array(),
		),
		'kbd'        => array(),
		'label'      => array(
			'for' => array(),
		),
		'legend'     => array(),
		'li'         => array(),
		'link'       => array(
			'rel'         => array(),
			'href'        => array(),
			'type'        => array(),
			'title'       => array(),
			'hreflang'    => array(),
			'crossorigin' => array(),
		),
		'main'       => array(),
		'map'        => array(
			'name' => array(),
		),
		'mark'       => array(),
		'menu'       => array(),
		'meta'       => array(
			'name'       => array(),
			'content'    => array(),
			'charset'    => array(),
			'http-equiv' => array(),
			'property'   => array(),
			'itemprop'   => array(),
		),
		'meter'      => array(
			'value'   => array(),
			'min'     => array(),
			'max'     => array(),
			'low'     => array(),
			'high'    => array(),
			'optimum' => array(),
		),
		'nav'        => array(),
		'noscript'   => array(),
		'object'     => array(
			'src'    => array(),
			'type'   => array(),
			'width'  => array(),
			'height' => array(),
			'form'   => array(),
		),
		'ol'         => array(),
		'optgroup'   => array(
			'label' => array(),
		),
		'option'     => array(
			'value'    => array(),
			'selected' => array(),
		),
		'output'     => array(
			'name' => array(),
		),
		'p'          => array(),
		'param'      => array(
			'name'  => array(),
			'value' => array(),
		),
		'pre'        => array(),
		'progress'   => array(
			'value' => array(),
			'max'   => array(),
		),
		'q'          => array(
			'cite' => array(),
		),
		'rp'         => array(),
		'rt'         => array(),
		'ruby'       => array(),
		's'          => array(),
		'samp'       => array(),
		'script'     => array(
			'src'     => array(),
			'type'    => array(),
			'charset' => array(),
			'async'   => array(),
			'defer'   => array(),
		),
		'section'    => array(),
		'select'     => array(
			'name' => array(),
		),
		'small'      => array(),
		'source'     => array(
			'src'  => array(),
			'type' => array(),
			'size' => array(),
		),
		'span'       => array(),
		'strong'     => array(),
		'style'      => array(
			'type' => array(),
		),
		'sub'        => array(),
		'summary'    => array(),
		'sup'        => array(),
		'table'      => array(),
		'tbody'      => array(),
		'td'         => array(
			'colspan' => array(),
			'rowspan' => array(),
		),
		'tfoot'      => array(),
		'th'         => array(
			'colspan' => array(),
			'rowspan' => array(),
		),
		'thead'      => array(),
		'time'       => array(
			'datetime' => array(),
		),
		'title'      => array(),
		'tr'         => array(),
		'track'      => array(
			'src'     => array(),
			'kind'    => array(),
			'label'   => array(),
			'srclang' => array(),
		),
		'u'          => array(),
		'ul'         => array(),
		'var'        => array(),
		'video'      => array(
			'width'              => array(),
			'height'             => array(),
			'controls'           => array(),
			'preload'            => array(),
			'poster'             => array(),
			'webkit-playsinline' => array(),
		),
		'wbr'        => array(),
	);
}
