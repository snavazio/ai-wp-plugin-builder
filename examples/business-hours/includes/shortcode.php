<?php
/**
 * Shortcode for displaying business hours.
 *
 * @package Bhrs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the business_hours shortcode.
 *
 * @param array $atts Shortcode attributes (unused).
 * @return string
 */
function bhrs_business_hours_shortcode( $atts ) {
	unset( $atts );

	$days = array(
		'monday'    => __( 'Monday', 'business-hours' ),
		'tuesday'   => __( 'Tuesday', 'business-hours' ),
		'wednesday' => __( 'Wednesday', 'business-hours' ),
		'thursday'  => __( 'Thursday', 'business-hours' ),
		'friday'    => __( 'Friday', 'business-hours' ),
		'saturday'  => __( 'Saturday', 'business-hours' ),
		'sunday'    => __( 'Sunday', 'business-hours' ),
	);

	$output  = '<div class="bhrs-business-hours">';
	$output .= '<table class="bhrs-hours-table">';
	$output .= '<thead><tr>';
	$output .= '<th>' . esc_html__( 'Day', 'business-hours' ) . '</th>';
	$output .= '<th>' . esc_html__( 'Hours', 'business-hours' ) . '</th>';
	$output .= '</tr></thead>';
	$output .= '<tbody>';

	foreach ( $days as $day_key => $day_label ) {
		$is_closed = get_option( 'bhrs_' . $day_key . '_closed', false );
		$open      = get_option( 'bhrs_' . $day_key . '_open', '' );
		$close     = get_option( 'bhrs_' . $day_key . '_close', '' );

		$output .= '<tr>';
		$output .= '<td class="bhrs-day">' . esc_html( $day_label ) . '</td>';
		$output .= '<td class="bhrs-hours">';

		if ( $is_closed ) {
			$output .= '<span class="bhrs-closed">' . esc_html__( 'Closed', 'business-hours' ) . '</span>';
		} elseif ( ! empty( $open ) && ! empty( $close ) ) {
			$output .= esc_html( $open ) . ' - ' . esc_html( $close );
		} elseif ( ! empty( $open ) || ! empty( $close ) ) {
			$output .= esc_html( $open . $close );
		} else {
			$output .= '<span class="bhrs-not-set">' . esc_html__( 'Not set', 'business-hours' ) . '</span>';
		}

		$output .= '</td>';
		$output .= '</tr>';
	}

	$output .= '</tbody>';
	$output .= '</table>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'business_hours', 'bhrs_business_hours_shortcode' );
