<?php

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load ) ) { fwrite( STDERR, "WordPress bootstrap not found.\n" ); exit( 1 ); }
require_once $wp_load;

function theme_schema_expect( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: $message\n" ); exit( 1 ); }
	echo "PASS: $message\n";
}

function theme_capture_clinic_schema( $clinic_id ) {
	global $post, $wp_query;
	$post = get_post( $clinic_id );
	setup_postdata( $post );
	$wp_query->is_singular       = true;
	$wp_query->is_single         = true;
	$wp_query->queried_object    = $post;
	$wp_query->queried_object_id = $clinic_id;

	ob_start();
	global360_output_schema();
	$output = ob_get_clean();
	preg_match( '#<script type="application/ld\+json">(.+)</script>#s', $output, $matches );
	return isset( $matches[1] ) ? json_decode( $matches[1], true ) : array();
}

$clinic_ids = get_posts(
	array(
		'post_type'      => 'clinic',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);

$complete_clinic = null;
$clinic_without_identity = null;
foreach ( array_map( 'absint', $clinic_ids ) as $clinic_id ) {
	$clinic = global360_theme_clinic( $clinic_id );
	$primary = is_array( $clinic ) ? ( $clinic['addresses'][0] ?? array() ) : array();
	if ( ! $complete_clinic && ! empty( $primary['street'] ) && ! empty( $primary['city'] ) && ! empty( $primary['state'] ) && ! empty( $primary['zip'] ) && ! empty( $clinic['website'] ) ) {
		$complete_clinic = $clinic;
	}
	if ( ! $clinic_without_identity && empty( $clinic['website'] ) ) {
		$clinic_without_identity = $clinic;
	}
}

theme_schema_expect( is_array( $complete_clinic ), 'fixture Clinic has a normalized address and Clinic-specific website' );
$schema = theme_capture_clinic_schema( (int) $complete_clinic['wp_id'] );
$primary = $complete_clinic['addresses'][0];
theme_schema_expect( 'MedicalClinic' === ( $schema['@type'] ?? '' ), 'Clinic schema renders a MedicalClinic entity' );
theme_schema_expect( ( $primary['street'] ?? '' ) === ( $schema['address']['streetAddress'] ?? '' ), 'Clinic schema uses Core normalized street address' );
theme_schema_expect( ( $primary['city'] ?? '' ) === ( $schema['address']['addressLocality'] ?? '' ), 'Clinic schema uses Core normalized locality' );
theme_schema_expect( ( $primary['state'] ?? '' ) === ( $schema['address']['addressRegion'] ?? '' ), 'Clinic schema uses Core normalized region' );
theme_schema_expect( ( $primary['zip'] ?? '' ) === ( $schema['address']['postalCode'] ?? '' ), 'Clinic schema uses Core normalized postal code' );
theme_schema_expect( array( $complete_clinic['website'] ) === ( $schema['sameAs'] ?? array() ), 'Clinic sameAs uses only the Clinic-specific website' );

if ( is_array( $clinic_without_identity ) ) {
	$schema_without_identity = theme_capture_clinic_schema( (int) $clinic_without_identity['wp_id'] );
	theme_schema_expect( ! isset( $schema_without_identity['sameAs'] ), 'Clinic without an identity URL does not inherit the network social profile' );
}

wp_reset_postdata();
echo "Theme Clinic schema integration tests passed.\n";
