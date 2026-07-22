<?php
/**
 * Dashboard widget for Content Stats.
 *
 * @package Cstw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output the content for the Content Stats dashboard widget.
 *
 * @return void
 */
function cstw_dashboard_widget_content() {
	$post_counts    = wp_count_posts( 'post' );
	$page_counts    = wp_count_posts( 'page' );
	$comment_counts = wp_count_comments();

	echo '<p>';
	echo esc_html( (string) $post_counts->publish ) . ' ' . esc_html__( 'Published Posts', 'content-stats' );
	echo '</p>';

	echo '<p>';
	echo esc_html( (string) $page_counts->publish ) . ' ' . esc_html__( 'Published Pages', 'content-stats' );
	echo '</p>';

	echo '<p>';
	echo esc_html( (string) $comment_counts->approved ) . ' ' . esc_html__( 'Approved Comments', 'content-stats' );
	echo '</p>';
}
