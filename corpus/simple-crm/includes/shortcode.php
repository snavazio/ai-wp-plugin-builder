<?php
/**
 * Contacts shortcode handler.
 *
 * @package Scrm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [contacts] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function scrm_contacts_shortcode( $atts ) {
	$defaults = array(
		'status' => '',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'contacts' );

	// Sanitize attributes.
	$status = sanitize_key( wp_unslash( $atts['status'] ) );

	$args = array(
		'post_type'      => 'scrm_contact',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( ! empty( $status ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'scrm_status',
				'field'    => 'slug',
				'terms'    => $status,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No contacts found.', 'simple-crm' ) . '</p>';
	}

	ob_start();
	?>
	<div class="scrm-contacts-list" data-status="<?php echo esc_attr( $status ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<div class="scrm-contact-item">
				<h3 class="scrm-contact-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php echo esc_html( get_the_title() ); ?>
					</a>
				</h3>
				<?php
				$email = get_post_meta( get_the_ID(), 'scrm_contact_email', true );
				$phone = get_post_meta( get_the_ID(), 'scrm_contact_phone', true );
				if ( $email ) {
					echo '<p class="scrm-contact-email"><span class="scrm-contact-label">' . esc_html__( 'Email:', 'simple-crm' ) . '</span> <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></p>';
				}
				if ( $phone ) {
					echo '<p class="scrm-contact-phone"><span class="scrm-contact-label">' . esc_html__( 'Phone:', 'simple-crm' ) . '</span> ' . esc_html( $phone ) . '</p>';
				}
				$statuses = get_the_terms( get_the_ID(), 'scrm_status' );
				if ( $statuses && ! is_wp_error( $statuses ) ) {
					$status_names = array();
					foreach ( $statuses as $status ) {
						$status_names[] = esc_html( $status->name );
					}
					echo '<p class="scrm-contact-status"><span class="scrm-contact-label">' . esc_html__( 'Status:', 'simple-crm' ) . '</span> ' . esc_html( implode( ', ', $status_names ) ) . '</p>';
				}
				?>
			</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'contacts', 'scrm_contacts_shortcode' );
