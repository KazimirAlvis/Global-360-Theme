<?php

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load ) ) { fwrite( STDERR, "WordPress bootstrap not found.\n" ); exit( 1 ); }
require_once $wp_load;

function theme_entity_seo_expect( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: $message\n" ); exit( 1 ); }
	echo "PASS: $message\n";
}

function theme_entity_seo_set_query( $post_id ) {
	global $post, $wp_query;
	$post = get_post( $post_id );
	setup_postdata( $post );
	$wp_query->is_singular       = true;
	$wp_query->is_single         = true;
	$wp_query->queried_object    = $post;
	$wp_query->queried_object_id = $post_id;
}

$bounded_text = global360_limit_seo_text( str_repeat( 'word ', 20 ), 60 );
theme_entity_seo_expect(
	mb_strlen( $bounded_text, 'UTF-8' ) <= 60 && '…' === mb_substr( $bounded_text, -1, 1, 'UTF-8' ),
	'word-safe title ceiling includes its ellipsis'
);
theme_entity_seo_expect(
	'Clinic & Doctor' === global360_limit_seo_text( 'Clinic &#038; Doctor', 60 ),
	'generated metadata decodes HTML entities'
);

$tested = array();
foreach ( array( 'clinic', 'doctor' ) as $post_type ) {
	$post_ids = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	theme_entity_seo_expect( ! empty( $post_ids ), $post_type . ' fixture exists' );
	foreach ( array_map( 'absint', $post_ids ) as $post_id ) {
		theme_entity_seo_set_query( $post_id );
		$title       = global360_build_fallback_title();
		$description = global360_build_fallback_description();
		$name        = html_entity_decode( get_the_title( $post_id ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		theme_entity_seo_expect( '' !== $title, $post_type . ' title is generated for post ' . $post_id );
		theme_entity_seo_expect( mb_strlen( $title, 'UTF-8' ) <= 60, $post_type . ' title is at most 60 characters for post ' . $post_id );
		theme_entity_seo_expect( false !== strpos( $title, $name ), $post_type . ' title retains the full entity name for post ' . $post_id );
		theme_entity_seo_expect( '' !== $description, $post_type . ' description is generated for post ' . $post_id );
		theme_entity_seo_expect( mb_strlen( $description, 'UTF-8' ) <= 160, $post_type . ' description is at most 160 characters for post ' . $post_id );
		theme_entity_seo_expect( false !== strpos( $description, $name ), $post_type . ' description retains the full entity name for post ' . $post_id );
		theme_entity_seo_expect( '.' === mb_substr( $description, -1, 1, 'UTF-8' ), $post_type . ' description remains a complete sentence for post ' . $post_id );

		$tested[ $post_type ] = $post_id;
	}
}

theme_entity_seo_set_query( $tested['clinic'] );
theme_entity_seo_expect(
	global360_build_fallback_title() === global360_filter_seopress_title_fallback( 'Unbounded generated title' ),
	'generated SEOPress Clinic title is replaced by the bounded entity title'
);
theme_entity_seo_expect(
	global360_build_fallback_description() === global360_filter_seopress_description_fallback( str_repeat( 'Long generated description ', 20 ) ),
	'generated SEOPress Clinic description is replaced by the bounded entity description'
);

$metadata_filter = static function ( $value, $object_id, $meta_key ) use ( $tested ) {
	if ( (int) $object_id !== (int) $tested['clinic'] ) {
		return $value;
	}
	if ( '_seopress_titles_title' === $meta_key ) {
		return 'Reviewed Clinic SEO Title';
	}
	if ( '_seopress_titles_desc' === $meta_key ) {
		return 'Reviewed Clinic SEO Description';
	}
	return $value;
};
add_filter( 'get_post_metadata', $metadata_filter, 10, 3 );
theme_entity_seo_expect(
	'Existing SEOPress Title' === global360_filter_seopress_title_fallback( 'Existing SEOPress Title' ),
	'explicit SEOPress title remains authoritative'
);
theme_entity_seo_expect(
	'Existing SEOPress Description' === global360_filter_seopress_description_fallback( 'Existing SEOPress Description' ),
	'explicit SEOPress description remains authoritative'
);
remove_filter( 'get_post_metadata', $metadata_filter, 10 );

wp_reset_postdata();
echo "Theme entity SEO metadata tests passed.\n";
