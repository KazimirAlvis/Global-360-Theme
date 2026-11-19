<?php

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load ) ) { fwrite( STDERR, "WordPress bootstrap not found.\n" ); exit( 1 ); }
require_once $wp_load;

function theme_state_social_expect( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: $message\n" ); exit( 1 ); }
	echo "PASS: $message\n";
}

$valid_states = global360_get_valid_state_slug_map();
theme_state_social_expect( isset( $valid_states['virginia'] ), 'Virginia is a valid state route' );

set_query_var( 'find_a_doctor_state', 'virginia' );
theme_state_social_expect( global360_is_valid_state_route(), 'valid state route is recognized' );

$title       = global360_build_fallback_title();
$description = global360_build_fallback_description();
$og_title    = global360_filter_state_og_title( '<meta property="og:title" content="Old Title">' );
$og_desc     = global360_filter_state_og_description( '<meta property="og:description" content="Old Description">' );
$tw_title    = global360_filter_state_twitter_title( '<meta name="twitter:title" content="Old Title">' );
$tw_desc     = global360_filter_state_twitter_description( '<meta name="twitter:description" content="Old Description">' );

theme_state_social_expect( false !== strpos( $og_title, esc_attr( $title ) ), 'Open Graph title uses the dynamic state title' );
theme_state_social_expect( false !== strpos( $og_desc, esc_attr( $description ) ), 'Open Graph description uses the dynamic state description' );
theme_state_social_expect( false !== strpos( $tw_title, esc_attr( $title ) ), 'Twitter title uses the dynamic state title' );
theme_state_social_expect( false !== strpos( $tw_desc, esc_attr( $description ) ), 'Twitter description uses the dynamic state description' );

$image = global360_get_state_social_image( 'facebook' );
theme_state_social_expect( ! empty( $image['url'] ), 'state route resolves an existing configured site image' );
$og_image = global360_filter_state_og_image( '<meta property="og:image" content="">' );
$tw_image = global360_filter_state_twitter_image( '' );
theme_state_social_expect( false !== strpos( $og_image, esc_url( $image['url'] ) ), 'empty Open Graph image receives the configured fallback' );
theme_state_social_expect( false !== strpos( $tw_image, esc_url( $image['url'] ) ), 'missing Twitter image receives the configured fallback' );

$existing_og = '<meta property="og:image" content="https://example.com/reviewed.jpg">';
$existing_tw = '<meta name="twitter:image" content="https://example.com/reviewed.jpg">';
theme_state_social_expect( $existing_og === global360_filter_state_og_image( $existing_og ), 'existing Open Graph image remains authoritative' );
theme_state_social_expect( $existing_tw === global360_filter_state_twitter_image( $existing_tw ), 'existing Twitter image remains authoritative' );

set_query_var( 'find_a_doctor_state', 'not-a-real-state' );
theme_state_social_expect( ! global360_is_valid_state_route(), 'invalid state route is rejected' );
theme_state_social_expect( 'unchanged' === global360_filter_state_og_title( 'unchanged' ), 'invalid route does not alter social metadata' );

set_query_var( 'find_a_doctor_state', '' );
theme_state_social_expect( 'unchanged' === global360_filter_state_twitter_image( 'unchanged' ), 'ordinary pages remain unaffected' );

echo "Theme state social metadata tests passed.\n";
