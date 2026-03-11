<?php
/**
 * Condition and Treatment page schema output.
 *
 * @package Global-360-Theme
 */

if ( ! function_exists( 'global360_condition_treatment_schema' ) ) {
	/**
	 * Output MedicalCondition or MedicalProcedure schema for configured pages.
	 */
	function global360_condition_treatment_schema() {
		if ( is_admin() ) {
			return;
		}

		$settings = get_option( '360_global_settings', array() );
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		$condition_name = sanitize_text_field( $settings['primary_condition'] ?? '' );
		$treatment_name = sanitize_text_field( $settings['primary_treatment'] ?? '' );

		$condition_page_id = isset( $settings['primary_condition_page'] ) ? (int) $settings['primary_condition_page'] : 0;
		$treatment_page_id = isset( $settings['primary_treatment_page'] ) ? (int) $settings['primary_treatment_page'] : 0;

		// Backward compatibility with existing settings keys used in this theme.
		if ( ! $condition_page_id && isset( $settings['condition_page'] ) ) {
			$condition_page_id = (int) $settings['condition_page'];
		}
		if ( ! $treatment_page_id && isset( $settings['treatment_page'] ) ) {
			$treatment_page_id = (int) $settings['treatment_page'];
		}

		if ( ! $condition_page_id && ! $treatment_page_id ) {
			return;
		}

		$schema = array();

		if ( $condition_page_id && is_page( $condition_page_id ) && '' !== $condition_name ) {
			$schema = array(
				'@context' => 'https://schema.org',
				'@type'    => 'MedicalCondition',
				'name'     => $condition_name,
			);

			if ( '' !== $treatment_name ) {
				$schema['possibleTreatment'] = array(
					'@type' => 'MedicalProcedure',
					'name'  => $treatment_name,
				);
			}
		} elseif ( $treatment_page_id && is_page( $treatment_page_id ) && '' !== $treatment_name ) {
			$schema = array(
				'@context'      => 'https://schema.org',
				'@type'         => 'MedicalProcedure',
				'name'          => $treatment_name,
				'procedureType' => 'Minimally Invasive Procedure',
			);

			if ( '' !== $condition_name ) {
				$schema['bodyLocation'] = $condition_name;
			}
		}

		if ( empty( $schema ) ) {
			return;
		}

		$json = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		if ( ! $json ) {
			return;
		}

		echo "\n";
		echo '<script type="application/ld+json">' . $json . '</script>';
		echo "\n";
	}
}

add_action( 'wp_head', 'global360_condition_treatment_schema', 101 );
