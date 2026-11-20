<?php

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load ) ) { fwrite( STDERR, "WordPress bootstrap not found.\n" ); exit( 1 ); }
require_once $wp_load;

function theme_utility_indexing_expect( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: $message\n" ); exit( 1 ); }
	echo "PASS: $message\n";
}

function theme_utility_indexing_set_query( $post_fixture ) {
	global $post, $wp_query;
	$post                         = get_post( $post_fixture );
	$wp_query->is_page           = true;
	$wp_query->is_singular       = true;
	$wp_query->queried_object    = $post;
	$wp_query->queried_object_id = $post->ID;
	setup_postdata( $post );
}

function theme_utility_indexing_schema_output( $callback ) {
	ob_start();
	call_user_func( $callback );
	return trim( (string) ob_get_clean() );
}

$utility_slugs = array( 'leave-a-review', 'sitemap', 'linktree', 'linktree-v2-recovery-test' );
foreach ( $utility_slugs as $slug ) {
	$page = get_page_by_path( $slug );
	theme_utility_indexing_expect( $page instanceof WP_Post, $slug . ' fixture exists' );
	$page_id = (int) $page->ID;
	theme_utility_indexing_set_query( $page );
	theme_utility_indexing_expect( global360_is_search_utility_page(), $slug . ' is recognized as a utility page' );
	theme_utility_indexing_expect(
		array( 'noindex', 'follow' ) === global360_filter_utility_page_seopress_robots( array( 'index, follow' ) ),
		$slug . ' receives SEOPress noindex/follow'
	);
	theme_utility_indexing_expect(
		null === global360_filter_utility_page_sitemap_url( array( 'loc' => get_permalink( $page_id ) ), $page_id ),
		$slug . ' is excluded from SEOPress XML sitemaps'
	);
	theme_utility_indexing_expect(
		'' === theme_utility_indexing_schema_output( 'global360_output_content_page_schema' ),
		$slug . ' emits no Theme MedicalWebPage schema'
	);
	theme_utility_indexing_expect(
		'' === theme_utility_indexing_schema_output( 'global360_output_breadcrumb_schema' ),
		$slug . ' emits no Theme breadcrumb schema'
	);
}

$reviews = get_page_by_path( 'patient-reviews' );
theme_utility_indexing_expect( $reviews instanceof WP_Post, 'Patient Reviews fixture exists' );
theme_utility_indexing_set_query( $reviews );
theme_utility_indexing_expect( ! global360_is_search_utility_page(), 'Patient Reviews remains an indexable content page' );
theme_utility_indexing_expect(
	array( 'index, follow' ) === global360_filter_utility_page_seopress_robots( array( 'index, follow' ) ),
	'Patient Reviews robots directives remain unchanged'
);
theme_utility_indexing_expect(
	false !== strpos( theme_utility_indexing_schema_output( 'global360_output_content_page_schema' ), 'MedicalWebPage' ),
	'Patient Reviews retains Theme content schema'
);
theme_utility_indexing_expect(
	false !== strpos( theme_utility_indexing_schema_output( 'global360_output_breadcrumb_schema' ), 'BreadcrumbList' ),
	'Patient Reviews retains Theme breadcrumb schema'
);

$about = get_page_by_path( 'about' );
theme_utility_indexing_expect( $about instanceof WP_Post, 'ordinary Page fixture exists' );
theme_utility_indexing_set_query( $about );
theme_utility_indexing_expect( ! global360_is_search_utility_page(), 'ordinary Pages remain unaffected' );

wp_reset_postdata();

echo "Theme utility-page indexing tests passed.\n";
