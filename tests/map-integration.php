<?php

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load ) ) { fwrite( STDERR, "WordPress bootstrap not found.\n" ); exit( 1 ); }
require_once $wp_load;
require_once dirname( __DIR__ ) . '/inc/map-utils.php';

function theme_map_expect( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: $message\n" ); exit( 1 ); }
	echo "PASS: $message\n";
}

$clinic_ids = get_posts(
	array(
		'post_type'              => 'clinic',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
	)
);

$clinic_id = 0;
$clinic_view = null;
foreach ( array_map( 'absint', $clinic_ids ) as $candidate_id ) {
	$candidate = global360_theme_clinic( $candidate_id );
	$address   = is_array( $candidate ) ? ( $candidate['addresses'][0] ?? array() ) : array();
	if ( is_array( $address ) && is_numeric( $address['latitude'] ?? null ) && is_numeric( $address['longitude'] ?? null ) ) {
		$clinic_id   = $candidate_id;
		$clinic_view = $candidate;
		break;
	}
}

theme_map_expect( $clinic_id > 0, 'fixture Clinic has stored canonical coordinates' );
$address = $clinic_view['addresses'][0];
$coords  = global360_get_address_coords( $address, false );
theme_map_expect( is_array( $coords ) && is_numeric( $coords['lat'] ) && is_numeric( $coords['lng'] ), 'Core coordinates adapt to the Theme map contract' );

$clinic_locations = global360_get_clinic_locations( $clinic_id, array( 'allow_geocode' => false ) );
theme_map_expect( ! empty( $clinic_locations ), 'Clinic with stored coordinates produces a normalized map location' );
theme_map_expect( isset( $clinic_locations[0]['coords'][0], $clinic_locations[0]['coords'][1] ), 'Clinic renderer input contains valid coordinates' );

$state_code = (string) ( $address['state'] ?? '' );
$state_name = function_exists( 'global360_platform' ) ? global360_platform()->states()->name( $state_code ) : '';
$state_locations = global360_get_state_locations( $state_code, $state_name, array( 'allow_geocode' => false ) );
$expected_markers = 0;
foreach ( array_map( 'absint', $clinic_ids ) as $candidate_id ) {
	$candidate = global360_theme_clinic( $candidate_id );
	if ( ! is_array( $candidate ) || ! in_array( $state_code, (array) ( $candidate['state_codes'] ?? array() ), true ) ) { continue; }
	foreach ( (array) ( $candidate['addresses'] ?? array() ) as $candidate_address ) {
		if ( is_array( $candidate_address ) && global360_get_address_coords( $candidate_address, false ) ) { $expected_markers++; }
	}
}
theme_map_expect( $expected_markers > 0 && $expected_markers === count( $state_locations ), 'populated state marker count matches valid stored coordinates' );

$geocode_requests = 0;
$http_guard = static function ( $preempt, $args, $url ) use ( &$geocode_requests ) {
	if ( false !== strpos( (string) $url, 'maps.googleapis.com/maps/api/geocode' ) ) { $geocode_requests++; }
	return $preempt;
};
add_filter( 'pre_http_request', $http_guard, 10, 3 );
global360_get_address_coords( array( 'street' => 'No coordinates' ), false );
global360_get_clinic_locations( $clinic_id, array( 'allow_geocode' => false ) );
global360_get_state_locations( $state_code, $state_name, array( 'allow_geocode' => false ) );
remove_filter( 'pre_http_request', $http_guard, 10 );
theme_map_expect( 0 === $geocode_requests, 'missing coordinates do not trigger frontend geocoding' );

ob_start();
global360_render_leaflet_map( $clinic_locations, array( 'map_id' => 'map-contract-test' ) );
$rendered_map = ob_get_clean();
theme_map_expect( false !== strpos( $rendered_map, 'leaflet.css' ) && false !== strpos( $rendered_map, 'leaflet.js' ), 'renderer includes required Leaflet assets' );
theme_map_expect( false !== strpos( $rendered_map, 'map-contract-test' ) && false !== strpos( $rendered_map, 'locations' ), 'renderer outputs map container and marker initialization data' );

echo "Theme map integration tests passed.\n";
