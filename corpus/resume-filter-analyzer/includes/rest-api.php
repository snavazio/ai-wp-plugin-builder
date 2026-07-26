<?php
/**
 * REST API endpoints for Resume Filter Analyzer.
 *
 * @package Rfa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register REST API routes.
 *
 * @return void
 */
function rfa_register_rest_routes() {
	register_rest_route(
		'rfa/v1',
		'/analyze',
		array(
			'methods'             => 'POST',
			'callback'            => 'rfa_analyze_callback',
			'permission_callback' => 'rfa_analyze_permission_callback',
			'args'                => array(
				'resume'              => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'rfa_sanitize_textarea',
				),
				'job_description'     => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'rfa_sanitize_textarea',
				),
				'company_name'        => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'research_company'    => array(
					'required'          => false,
					'type'              => 'boolean',
					'default'           => false,
					'sanitize_callback' => 'rest_sanitize_boolean',
				),
				'provide_suggestions' => array(
					'required'          => false,
					'type'              => 'boolean',
					'default'           => false,
					'sanitize_callback' => 'rest_sanitize_boolean',
				),
			),
		)
	);

	register_rest_route(
		'rfa/v1',
		'/export',
		array(
			'methods'             => 'POST',
			'callback'            => 'rfa_export_callback',
			'permission_callback' => 'rfa_export_permission_callback',
			'args'                => array(
				'analysis'   => array(
					'required' => true,
					'type'     => 'object',
				),
				'format'     => array(
					'required'          => false,
					'type'              => 'string',
					'default'           => 'text',
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => 'rfa_validate_export_format',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'rfa_register_rest_routes' );

/**
 * Sanitize textarea input (remove slashes and sanitize).
 *
 * @param string $value Input value.
 * @return string Sanitized value.
 */
function rfa_sanitize_textarea( $value ) {
	return sanitize_textarea_field( wp_unslash( $value ) );
}

/**
 * Validate export format parameter.
 *
 * @param string $value Format value.
 * @return bool True if valid.
 */
function rfa_validate_export_format( $value ) {
	return in_array( $value, array( 'pdf', 'text' ), true );
}

/**
 * Permission callback for analyze endpoint.
 *
 * @param WP_REST_Request $request Request object.
 * @return bool|WP_Error True if allowed, WP_Error if rate limited.
 */
function rfa_analyze_permission_callback( $request ) {
	// Capability check.
	if ( ! current_user_can( 'read' ) ) {
		return new WP_Error(
			'rest_forbidden',
			__( 'You do not have permission to access this endpoint.', 'resume-filter-analyzer' ),
			array( 'status' => 403 )
		);
	}

	// Rate limiting check.
	if ( ! rfa_check_rate_limit( 'analyze' ) ) {
		return new WP_Error(
			'rate_limit_exceeded',
			__( 'Too many requests. Please try again later.', 'resume-filter-analyzer' ),
			array( 'status' => 429 )
		);
	}

	return true;
}

/**
 * Permission callback for export endpoint.
 *
 * @param WP_REST_Request $request Request object.
 * @return bool|WP_Error True if allowed, WP_Error if rate limited.
 */
function rfa_export_permission_callback( $request ) {
	// Capability check.
	if ( ! current_user_can( 'read' ) ) {
		return new WP_Error(
			'rest_forbidden',
			__( 'You do not have permission to access this endpoint.', 'resume-filter-analyzer' ),
			array( 'status' => 403 )
		);
	}

	// Rate limiting check.
	if ( ! rfa_check_rate_limit( 'export' ) ) {
		return new WP_Error(
			'rate_limit_exceeded',
			__( 'Too many requests. Please try again later.', 'resume-filter-analyzer' ),
			array( 'status' => 429 )
		);
	}

	return true;
}

/**
 * Check rate limit for an endpoint.
 *
 * @param string $endpoint Endpoint name.
 * @return bool True if allowed, false if rate limited.
 */
function rfa_check_rate_limit( $endpoint ) {
	// Get user identifier (IP or user ID).
	$identifier = rfa_get_request_identifier();
	$limit      = 10; // Max requests per hour.
	$transient  = 'rfa_rate_' . $endpoint . '_' . $identifier;

	$count = get_transient( $transient );

	if ( false === $count ) {
		set_transient( $transient, 1, HOUR_IN_SECONDS );
		return true;
	}

	if ( $count >= $limit ) {
		return false;
	}

	set_transient( $transient, $count + 1, HOUR_IN_SECONDS );
	return true;
}

/**
 * Get request identifier for rate limiting.
 *
 * @return string Hashed identifier.
 */
function rfa_get_request_identifier() {
	if ( is_user_logged_in() ) {
		return 'user_' . get_current_user_id();
	}

	// Use IP address for anonymous users.
	$ip = '';
	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
	}

	return 'ip_' . md5( $ip );
}

/**
 * Analyze endpoint callback.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error Response or error.
 */
function rfa_analyze_callback( $request ) {
	$resume              = $request->get_param( 'resume' );
	$job_description     = $request->get_param( 'job_description' );
	$company_name        = $request->get_param( 'company_name' );
	$research_company    = $request->get_param( 'research_company' );
	$provide_suggestions = $request->get_param( 'provide_suggestions' );

	// Validate required fields are not empty.
	if ( empty( $resume ) || empty( $job_description ) || empty( $company_name ) ) {
		return new WP_Error(
			'missing_required_fields',
			__( 'Resume, job description, and company name are required.', 'resume-filter-analyzer' ),
			array( 'status' => 400 )
		);
	}

	// Perform analysis.
	$analysis = rfa_perform_analysis( $resume, $job_description, $company_name, $research_company, $provide_suggestions );

	// Store results in transient for export.
	$result_id = 'rfa_result_' . rfa_get_request_identifier() . '_' . time();
	set_transient( $result_id, $analysis, HOUR_IN_SECONDS );

	$analysis['result_id'] = $result_id;

	return rest_ensure_response( $analysis );
}

/**
 * Perform resume analysis.
 *
 * @param string $resume Resume text.
 * @param string $job_description Job description text.
 * @param string $company_name Company name.
 * @param bool   $research_company Whether to research company.
 * @param bool   $provide_suggestions Whether to provide suggestions.
 * @return array Analysis results.
 */
function rfa_perform_analysis( $resume, $job_description, $company_name, $research_company, $provide_suggestions ) {
	// Extract keywords from job description.
	$job_keywords = rfa_extract_keywords( $job_description );

	// Extract keywords from resume.
	$resume_keywords = rfa_extract_keywords( $resume );

	// Find matching keywords.
	$matches = array_intersect( $job_keywords, $resume_keywords );

	// Find missing keywords.
	$missing = array_diff( $job_keywords, $resume_keywords );

	// Calculate match score.
	$match_score = empty( $job_keywords ) ? 0 : ( count( $matches ) / count( $job_keywords ) ) * 100;

	// Identify strengths and weaknesses.
	$strengths  = rfa_identify_strengths( $resume, $matches );
	$weaknesses = rfa_identify_weaknesses( $resume, $missing );

	$analysis = array(
		'company_name'  => $company_name,
		'match_score'   => round( $match_score, 1 ),
		'matched_keywords' => array_values( $matches ),
		'missing_keywords' => array_values( $missing ),
		'strengths'     => $strengths,
		'weaknesses'    => $weaknesses,
	);

	// Optional: research company.
	if ( $research_company ) {
		$analysis['company_info'] = rfa_research_company( $company_name );
	}

	// Optional: provide suggestions.
	if ( $provide_suggestions ) {
		$analysis['suggestions'] = rfa_generate_suggestions( $resume, $job_description, $missing, $strengths, $weaknesses );
	}

	return $analysis;
}

/**
 * Extract important keywords from text.
 *
 * @param string $text Input text.
 * @return array Keywords.
 */
function rfa_extract_keywords( $text ) {
	// Convert to lowercase.
	$text = strtolower( $text );

	// Remove common words (stop words).
	$stop_words = array(
		'the',
		'a',
		'an',
		'and',
		'or',
		'but',
		'in',
		'on',
		'at',
		'to',
		'for',
		'of',
		'with',
		'by',
		'from',
		'as',
		'is',
		'was',
		'are',
		'were',
		'been',
		'be',
		'have',
		'has',
		'had',
		'do',
		'does',
		'did',
		'will',
		'would',
		'should',
		'could',
		'may',
		'might',
		'must',
		'can',
		'this',
		'that',
		'these',
		'those',
		'i',
		'you',
		'he',
		'she',
		'it',
		'we',
		'they',
	);

	// Extract words (2+ characters, alphanumeric with hyphens).
	preg_match_all( '/\b[a-z0-9\-]{2,}\b/', $text, $matches );
	$words = $matches[0];

	// Filter stop words.
	$keywords = array_diff( $words, $stop_words );

	// Count frequency and get top keywords.
	$frequency = array_count_values( $keywords );
	arsort( $frequency );

	// Return top 50 keywords.
	return array_keys( array_slice( $frequency, 0, 50, true ) );
}

/**
 * Identify strengths based on matched keywords.
 *
 * @param string $resume Resume text.
 * @param array  $matches Matched keywords.
 * @return array Strengths.
 */
function rfa_identify_strengths( $resume, $matches ) {
	$strengths = array();

	if ( count( $matches ) > 10 ) {
		$strengths[] = __( 'Strong keyword alignment with job requirements', 'resume-filter-analyzer' );
	}

	// Check for years of experience.
	if ( preg_match( '/(\d+)\+?\s*years?/i', $resume, $exp_matches ) ) {
		$years = intval( $exp_matches[1] );
		if ( $years >= 5 ) {
			/* translators: %d: number of years */
			$strengths[] = sprintf( __( 'Substantial experience (%d+ years)', 'resume-filter-analyzer' ), $years );
		}
	}

	// Check for education.
	$education_keywords = array( 'bachelor', 'master', 'phd', 'degree', 'university', 'college' );
	foreach ( $education_keywords as $edu ) {
		if ( stripos( $resume, $edu ) !== false ) {
			$strengths[] = __( 'Relevant educational background', 'resume-filter-analyzer' );
			break;
		}
	}

	if ( empty( $strengths ) ) {
		$strengths[] = __( 'Resume contains relevant keywords', 'resume-filter-analyzer' );
	}

	return $strengths;
}

/**
 * Identify weaknesses based on missing keywords.
 *
 * @param string $resume Resume text.
 * @param array  $missing Missing keywords.
 * @return array Weaknesses.
 */
function rfa_identify_weaknesses( $resume, $missing ) {
	$weaknesses = array();

	if ( count( $missing ) > 10 ) {
		$weaknesses[] = __( 'Multiple important keywords missing from resume', 'resume-filter-analyzer' );
	}

	// Check if resume is too short.
	$word_count = str_word_count( $resume );
	if ( $word_count < 100 ) {
		$weaknesses[] = __( 'Resume may be too brief (lacks detail)', 'resume-filter-analyzer' );
	}

	if ( empty( $weaknesses ) && ! empty( $missing ) ) {
		$weaknesses[] = __( 'Some job requirements not explicitly mentioned', 'resume-filter-analyzer' );
	}

	if ( empty( $weaknesses ) ) {
		$weaknesses[] = __( 'No significant weaknesses identified', 'resume-filter-analyzer' );
	}

	return $weaknesses;
}

/**
 * Research company information.
 *
 * @param string $company_name Company name.
 * @return array Company information.
 */
function rfa_research_company( $company_name ) {
	// Placeholder implementation - in production, this would call external APIs.
	return array(
		'name'        => $company_name,
		'description' => __( 'Company research not implemented in this version.', 'resume-filter-analyzer' ),
		'note'        => __( 'This feature would integrate with external APIs like Glassdoor or LinkedIn in a production environment.', 'resume-filter-analyzer' ),
	);
}

/**
 * Generate improvement suggestions.
 *
 * @param string $resume Resume text.
 * @param string $job_description Job description.
 * @param array  $missing Missing keywords.
 * @param array  $strengths Strengths.
 * @param array  $weaknesses Weaknesses.
 * @return array Suggestions with reasoning.
 */
function rfa_generate_suggestions( $resume, $job_description, $missing, $strengths, $weaknesses ) {
	$suggestions = array();

	// Suggest adding missing keywords.
	if ( ! empty( $missing ) ) {
		$top_missing = array_slice( $missing, 0, 5 );
		$suggestions[] = array(
			'suggestion' => __( 'Incorporate missing keywords naturally into your resume', 'resume-filter-analyzer' ),
			'reasoning'  => sprintf(
				/* translators: %s: comma-separated list of keywords */
				__( 'These keywords appear in the job description but not in your resume: %s', 'resume-filter-analyzer' ),
				implode( ', ', $top_missing )
			),
		);
	}

	// Suggest quantifying achievements.
	if ( ! preg_match( '/\d+%|\$\d+|\d+\s*(users|customers|clients|projects)/i', $resume ) ) {
		$suggestions[] = array(
			'suggestion' => __( 'Add quantifiable achievements and metrics', 'resume-filter-analyzer' ),
			'reasoning'  => __( 'Numbers and metrics make your accomplishments more concrete and impressive to hiring filters and recruiters.', 'resume-filter-analyzer' ),
		);
	}

	// Suggest adding action verbs.
	$action_verbs = array( 'achieved', 'developed', 'implemented', 'managed', 'led', 'created', 'improved' );
	$has_action_verbs = false;
	foreach ( $action_verbs as $verb ) {
		if ( stripos( $resume, $verb ) !== false ) {
			$has_action_verbs = true;
			break;
		}
	}
	if ( ! $has_action_verbs ) {
		$suggestions[] = array(
			'suggestion' => __( 'Use strong action verbs to describe your experience', 'resume-filter-analyzer' ),
			'reasoning'  => __( 'Action verbs like "achieved", "developed", "implemented", and "led" make your resume more dynamic and impactful.', 'resume-filter-analyzer' ),
		);
	}

	// If no specific suggestions, provide general advice.
	if ( empty( $suggestions ) ) {
		$suggestions[] = array(
			'suggestion' => __( 'Tailor your resume to mirror the job description language', 'resume-filter-analyzer' ),
			'reasoning'  => __( 'Your resume already has good keyword alignment. Continue to use the same terminology and phrases found in job descriptions.', 'resume-filter-analyzer' ),
		);
	}

	return $suggestions;
}

/**
 * Export endpoint callback.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error Response or error.
 */
function rfa_export_callback( $request ) {
	$analysis = $request->get_param( 'analysis' );
	$format   = $request->get_param( 'format' );

	if ( empty( $analysis ) ) {
		return new WP_Error(
			'missing_analysis_data',
			__( 'Analysis data is required for export.', 'resume-filter-analyzer' ),
			array( 'status' => 400 )
		);
	}

	if ( 'pdf' === $format ) {
		$content = rfa_generate_pdf_content( $analysis );
		$mime    = 'application/pdf';
		$ext     = 'pdf';
	} else {
		$content = rfa_generate_text_content( $analysis );
		$mime    = 'text/plain';
		$ext     = 'txt';
	}

	$filename = 'resume-analysis-' . gmdate( 'Y-m-d-His' ) . '.' . $ext;

	return new WP_REST_Response(
		array(
			'content'  => base64_encode( $content ),
			'filename' => $filename,
			'mime'     => $mime,
		),
		200
	);
}

/**
 * Generate text content for export.
 *
 * @param array $analysis Analysis data.
 * @return string Text content.
 */
function rfa_generate_text_content( $analysis ) {
	$output = "RESUME FILTER ANALYSIS REPORT\n";
	$output .= str_repeat( '=', 50 ) . "\n\n";

	if ( isset( $analysis['company_name'] ) ) {
		$output .= 'Company: ' . $analysis['company_name'] . "\n\n";
	}

	if ( isset( $analysis['match_score'] ) ) {
		$output .= 'Match Score: ' . $analysis['match_score'] . "%\n\n";
	}

	if ( ! empty( $analysis['matched_keywords'] ) ) {
		$output .= "Matched Keywords:\n";
		$output .= '- ' . implode( "\n- ", $analysis['matched_keywords'] ) . "\n\n";
	}

	if ( ! empty( $analysis['missing_keywords'] ) ) {
		$output .= "Missing Keywords:\n";
		$output .= '- ' . implode( "\n- ", $analysis['missing_keywords'] ) . "\n\n";
	}

	if ( ! empty( $analysis['strengths'] ) ) {
		$output .= "Strengths:\n";
		foreach ( $analysis['strengths'] as $strength ) {
			$output .= '- ' . $strength . "\n";
		}
		$output .= "\n";
	}

	if ( ! empty( $analysis['weaknesses'] ) ) {
		$output .= "Weaknesses:\n";
		foreach ( $analysis['weaknesses'] as $weakness ) {
			$output .= '- ' . $weakness . "\n";
		}
		$output .= "\n";
	}

	if ( ! empty( $analysis['suggestions'] ) ) {
		$output .= "Improvement Suggestions:\n";
		$output .= str_repeat( '-', 50 ) . "\n";
		foreach ( $analysis['suggestions'] as $idx => $suggestion ) {
			$output .= "\n" . ( $idx + 1 ) . '. ' . $suggestion['suggestion'] . "\n";
			$output .= '   Reasoning: ' . $suggestion['reasoning'] . "\n";
		}
	}

	if ( ! empty( $analysis['company_info'] ) ) {
		$output .= "\n" . str_repeat( '-', 50 ) . "\n";
		$output .= "Company Research:\n";
		if ( isset( $analysis['company_info']['description'] ) ) {
			$output .= $analysis['company_info']['description'] . "\n";
		}
		if ( isset( $analysis['company_info']['note'] ) ) {
			$output .= $analysis['company_info']['note'] . "\n";
		}
	}

	$output .= "\n" . str_repeat( '=', 50 ) . "\n";
	$output .= 'Generated by Resume Filter Analyzer' . "\n";
	$output .= 'Date: ' . gmdate( 'Y-m-d H:i:s' ) . " UTC\n";

	return $output;
}

/**
 * Generate PDF content for export.
 *
 * @param array $analysis Analysis data.
 * @return string PDF content (placeholder - returns text for now).
 */
function rfa_generate_pdf_content( $analysis ) {
	// Note: Real PDF generation would require a library like TCPDF or FPDF.
	// For this implementation, we'll return a formatted text version.
	// In production, integrate a PDF library.
	return rfa_generate_text_content( $analysis );
}
