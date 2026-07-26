<?php
/**
 * Shortcode: [rfa_analyzer]
 *
 * @package Rfa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the analyzer form shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function rfa_analyzer_shortcode( $atts ) {
	wp_enqueue_style( 'rfa-analyzer', plugins_url( 'assets/css/analyzer.css', RFA_FILE ), array(), RFA_VERSION );
	wp_enqueue_script( 'rfa-analyzer', plugins_url( 'assets/js/analyzer.js', RFA_FILE ), array( 'jquery' ), RFA_VERSION, true );

	wp_localize_script(
		'rfa-analyzer',
		'rfaData',
		array(
			'restUrl'   => esc_url_raw( rest_url( 'rfa/v1' ) ),
			'nonce'     => wp_create_nonce( 'wp_rest' ),
			'labels'    => array(
				'analyzing' => esc_html__( 'Analyzing...', 'resume-filter-analyzer' ),
				'exporting' => esc_html__( 'Exporting...', 'resume-filter-analyzer' ),
				'error'     => esc_html__( 'An error occurred. Please try again.', 'resume-filter-analyzer' ),
			),
		)
	);

	ob_start();
	?>
	<div class="rfa-analyzer-container" role="main">
		<form id="rfa-analyzer-form" class="rfa-form" aria-labelledby="rfa-form-title">
			<h2 id="rfa-form-title"><?php esc_html_e( 'Resume Filter Analyzer', 'resume-filter-analyzer' ); ?></h2>

			<div class="rfa-form-group">
				<label for="rfa-resume">
					<?php esc_html_e( 'Resume Text', 'resume-filter-analyzer' ); ?>
					<span class="required" aria-label="<?php esc_attr_e( 'required', 'resume-filter-analyzer' ); ?>">*</span>
				</label>
				<textarea
					id="rfa-resume"
					name="resume"
					rows="10"
					required
					aria-required="true"
					aria-describedby="rfa-resume-desc"
				><?php echo esc_textarea( '' ); ?></textarea>
				<p id="rfa-resume-desc" class="description">
					<?php esc_html_e( 'Paste your complete resume text here.', 'resume-filter-analyzer' ); ?>
				</p>
			</div>

			<div class="rfa-form-group">
				<label for="rfa-job-description">
					<?php esc_html_e( 'Job Description', 'resume-filter-analyzer' ); ?>
					<span class="required" aria-label="<?php esc_attr_e( 'required', 'resume-filter-analyzer' ); ?>">*</span>
				</label>
				<textarea
					id="rfa-job-description"
					name="job_description"
					rows="10"
					required
					aria-required="true"
					aria-describedby="rfa-job-desc"
				><?php echo esc_textarea( '' ); ?></textarea>
				<p id="rfa-job-desc" class="description">
					<?php esc_html_e( 'Paste the job description you are applying for.', 'resume-filter-analyzer' ); ?>
				</p>
			</div>

			<div class="rfa-form-group">
				<label for="rfa-company-name">
					<?php esc_html_e( 'Company Name', 'resume-filter-analyzer' ); ?>
					<span class="required" aria-label="<?php esc_attr_e( 'required', 'resume-filter-analyzer' ); ?>">*</span>
				</label>
				<input
					type="text"
					id="rfa-company-name"
					name="company_name"
					required
					aria-required="true"
					aria-describedby="rfa-company-desc"
					value="<?php echo esc_attr( '' ); ?>"
				/>
				<p id="rfa-company-desc" class="description">
					<?php esc_html_e( 'Enter the name of the company you are applying to.', 'resume-filter-analyzer' ); ?>
				</p>
			</div>

			<div class="rfa-form-group rfa-checkbox-group">
				<label>
					<input
						type="checkbox"
						id="rfa-research-company"
						name="research_company"
						value="1"
					/>
					<?php esc_html_e( 'Research company (may increase analysis time)', 'resume-filter-analyzer' ); ?>
				</label>
			</div>

			<div class="rfa-form-group rfa-checkbox-group">
				<label>
					<input
						type="checkbox"
						id="rfa-provide-suggestions"
						name="provide_suggestions"
						value="1"
					/>
					<?php esc_html_e( 'Provide improvement suggestions', 'resume-filter-analyzer' ); ?>
				</label>
			</div>

			<div class="rfa-form-actions">
				<button type="submit" class="rfa-button rfa-button-primary">
					<?php esc_html_e( 'Analyze Resume', 'resume-filter-analyzer' ); ?>
				</button>
			</div>
		</form>

		<div id="rfa-results" class="rfa-results" style="display: none;" role="region" aria-live="polite">
			<h3><?php esc_html_e( 'Analysis Results', 'resume-filter-analyzer' ); ?></h3>
			<div id="rfa-results-content"></div>

			<div class="rfa-export-actions">
				<button type="button" id="rfa-export-pdf" class="rfa-button rfa-button-secondary">
					<?php esc_html_e( 'Export as PDF', 'resume-filter-analyzer' ); ?>
				</button>
				<button type="button" id="rfa-export-text" class="rfa-button rfa-button-secondary">
					<?php esc_html_e( 'Export as Text', 'resume-filter-analyzer' ); ?>
				</button>
			</div>
		</div>

		<div id="rfa-loading" class="rfa-loading" style="display: none;" role="status" aria-live="polite">
			<span class="rfa-spinner"></span>
			<span class="rfa-loading-text"></span>
		</div>

		<div id="rfa-error" class="rfa-error" style="display: none;" role="alert" aria-live="assertive"></div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'rfa_analyzer', 'rfa_analyzer_shortcode' );
