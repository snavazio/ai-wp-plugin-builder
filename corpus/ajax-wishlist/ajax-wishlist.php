<?php
/**
 * Plugin Name:       AJAX Wishlist
 * Description:       Adds a [wishlist_button] shortcode for users to add the current post to a per-user wishlist via AJAX (logged-out allowed, stored in a cookie-keyed option).
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-wishlist
 *
 * @package Ajaxw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AJAXW_VERSION', '1.0.0' );
define( 'AJAXW_FILE', __FILE__ );
define( 'AJAXW_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ajaxw_load_textdomain() {
	load_plugin_textdomain( 'ajax-wishlist', false, dirname( plugin_basename( AJAXW_FILE ) ) . '/languages' );
}
add_action( 'init', 'ajaxw_load_textdomain' );

/**
 * Register the wishlist_button shortcode.
 *
 * @return void
 */
function ajaxw_register_shortcodes() {
	add_shortcode( 'wishlist_button', 'ajaxw_shortcode_wishlist_button' );
}
add_action( 'init', 'ajaxw_register_shortcodes' );

/**
 * Shortcode handler for [wishlist_button].
 *
 * @return string
 */
function ajaxw_shortcode_wishlist_button() {
	$post_id = get_the_ID();
	$button  = '<button class="ajaxw-wishlist-button" data-post-id="' . esc_attr( (string) $post_id ) . '">' . esc_html__( 'Add to Wishlist', 'ajax-wishlist' ) . '</button>';
	return $button;
}

/**
 * Enqueue frontend scripts.
 *
 * @return void
 */
function ajaxw_enqueue_scripts() {
	wp_enqueue_script(
		'ajaxw-wishlist',
		AJAXW_PATH . 'assets/js/wishlist.js',
		array( 'jquery' ),
		AJAXW_VERSION,
		true
	);
	wp_localize_script(
		'ajaxw-wishlist',
		'ajaxw_wishlist',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ajaxw_add_to_wishlist' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ajaxw_enqueue_scripts' );

/**
 * Handle AJAX request to add post to wishlist.
 *
 * @return void
 */
function ajaxw_handle_add_to_wishlist() {
	check_ajax_referer( 'ajaxw_add_to_wishlist', 'nonce' );

	if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( 'You do not have permission to add to the wishlist.' );
	}

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	if ( ! $post_id ) {
		wp_send_json_error( 'Invalid post ID.' );
	}

	$cookie_value = isset( $_COOKIE['ajaxw_user_id'] ) ? sanitize_key( $_COOKIE['ajaxw_user_id'] ) : null;
	if ( ! $cookie_value ) {
		$cookie_value  = wp_generate_password( 32, false, false );
		$cookie_path   = str_replace( home_url(), '', site_url() );
		$cookie_domain = parse_url( home_url(), PHP_URL_HOST );
		setcookie( 'ajaxw_user_id', $cookie_value, time() + YEAR_IN_SECONDS, $cookie_path, $cookie_domain, is_ssl() );
	}

	$option_name = 'ajaxw_wishlist_' . $cookie_value;
	$wishlist    = get_option( $option_name, array() );

	if ( ! in_array( $post_id, $wishlist ) ) {
		$wishlist[] = $post_id;
		update_option( $option_name, $wishlist );
	}

	wp_send_json_success( array( 'wishlist' => $wishlist ) );
}
add_action( 'wp_ajax_ajaxw_add_to_wishlist', 'ajaxw_handle_add_to_wishlist' );
add_action( 'wp_ajax_nopriv_ajaxw_add_to_wishlist', 'ajaxw_handle_add_to_wishlist' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function ajaxw_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ajaxw_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function ajaxw_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ajaxw_deactivate' );
