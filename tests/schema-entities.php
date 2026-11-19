<?php

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load ) ) { fwrite( STDERR, "WordPress bootstrap not found.\n" ); exit( 1 ); }
require_once $wp_load;

function theme_entity_expect( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: $message\n" ); exit( 1 ); }
	echo "PASS: $message\n";
}

function theme_entity_capture_singular_schema( $post_id ) {
	global $post, $wp_query;
	$post = get_post( $post_id );
	setup_postdata( $post );
	$wp_query->is_singular       = true;
	$wp_query->is_single         = true;
	$wp_query->queried_object    = $post;
	$wp_query->queried_object_id = $post_id;

	ob_start();
	global360_output_schema();
	return ob_get_clean();
}

theme_entity_expect( 'Ally Vascular & Pain Centers' === global360_schema_text( 'Ally Vascular &#038; Pain Centers' ), 'numeric ampersand entity is decoded' );
theme_entity_expect( "Doctor's Clinic" === global360_schema_text( 'Doctor&#039;s Clinic' ), 'apostrophe entity is decoded' );
theme_entity_expect( '"Quoted" Clinic' === global360_schema_text( '&quot;Quoted&quot; Clinic' ), 'quotation entities are decoded' );
theme_entity_expect( 'Clínica São José' === global360_schema_text( 'Clínica São José' ), 'accented Unicode text is preserved' );
theme_entity_expect( '' === global360_schema_text( array( 'unsafe' ) ), 'non-scalar schema text fails safely' );

$clinic_ids = get_posts(
	array(
		'post_type'      => 'clinic',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);

$entity_clinic_id = 0;
$entity_clinic_name = '';
foreach ( array_map( 'absint', $clinic_ids ) as $clinic_id ) {
	$title = (string) get_the_title( $clinic_id );
	if ( false !== strpos( $title, '&' ) ) {
		$entity_clinic_id = $clinic_id;
		$entity_clinic_name = global360_schema_text( $title );
		break;
	}
}

theme_entity_expect( $entity_clinic_id > 0, 'fixture Clinic has an entity-encoded title' );
$clinic_output = theme_entity_capture_singular_schema( $entity_clinic_id );
theme_entity_expect( false === strpos( $clinic_output, '&#' ), 'Clinic JSON-LD contains no encoded HTML entities' );
theme_entity_expect( false !== strpos( $clinic_output, '"name":"' . $entity_clinic_name . '"' ), 'Clinic JSON-LD contains the decoded name' );

$doctor_ids = get_posts(
	array(
		'post_type'      => 'doctor',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);
$related_doctor_id = 0;
foreach ( array_map( 'absint', $doctor_ids ) as $doctor_id ) {
	$doctor = global360_theme_doctor( $doctor_id );
	if ( is_array( $doctor ) && in_array( $entity_clinic_id, array_map( 'absint', (array) ( $doctor['clinic_ids'] ?? array() ) ), true ) ) {
		$related_doctor_id = $doctor_id;
		break;
	}
}

theme_entity_expect( $related_doctor_id > 0, 'fixture Doctor is related to the encoded-title Clinic' );
$doctor_output = theme_entity_capture_singular_schema( $related_doctor_id );
theme_entity_expect( false === strpos( $doctor_output, '&#' ), 'Doctor relationship JSON-LD contains no encoded HTML entities' );
theme_entity_expect( false !== strpos( $doctor_output, '"name":"' . $entity_clinic_name . '"' ), 'Doctor worksFor uses the decoded Clinic name' );

$clinic = global360_theme_clinic( $entity_clinic_id );
$state_code = is_array( $clinic ) ? (string) ( $clinic['addresses'][0]['state'] ?? '' ) : '';
$state_name = $state_code && function_exists( 'global360_platform' ) ? global360_platform()->states()->name( $state_code ) : '';
$state_slug = sanitize_title( $state_name );
theme_entity_expect( '' !== $state_slug, 'fixture Clinic belongs to a valid state route' );
set_query_var( 'find_a_doctor_state', $state_slug );
ob_start();
global360_output_find_a_doctor_state_schema();
$state_output = ob_get_clean();
theme_entity_expect( false === strpos( $state_output, '&#' ), 'state ItemList JSON-LD contains no encoded HTML entities' );
theme_entity_expect( false !== strpos( $state_output, '"name":"' . $entity_clinic_name . '"' ), 'state ItemList uses the decoded Clinic name' );

set_query_var( 'find_a_doctor_state', '' );
wp_reset_postdata();
echo "Theme schema entity tests passed.\n";
