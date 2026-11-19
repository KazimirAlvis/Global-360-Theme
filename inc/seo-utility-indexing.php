<?php
/**
 * Search-indexing policy for public utility pages.
 *
 * These pages remain publicly accessible and their links remain crawlable, but
 * they are not useful standalone search results and should not enter XML
 * sitemaps. Patient Reviews intentionally remains an indexable content page.
 *
 * @package Global-360-Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine whether a page is a search utility rather than an organic result.
 *
 * @param WP_Post|int|null $post Optional post context.
 * @return bool
 */
function global360_is_search_utility_page( $post = null ) {
	if ( null === $post ) {
		$post = get_queried_object();
	} elseif ( is_numeric( $post ) ) {
		$post = get_post( (int) $post );
	}

	if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
		return false;
	}

	$template = get_page_template_slug( $post->ID );
	if ( in_array( $template, array( 'page-linktree.php', 'page-linktree-v2.php' ), true ) ) {
		return true;
	}

	return in_array( $post->post_name, array( 'leave-a-review', 'sitemap' ), true );
}

/**
 * Mark utility pages noindex/follow for WordPress core robots output.
 *
 * @param array $robots Robots directives.
 * @return array
 */
function global360_filter_utility_page_wp_robots( $robots ) {
	if ( ! global360_is_search_utility_page() ) {
		return $robots;
	}

	unset( $robots['index'], $robots['nofollow'] );
	$robots['noindex'] = true;
	$robots['follow']  = true;

	return $robots;
}
add_filter( 'wp_robots', 'global360_filter_utility_page_wp_robots' );

/**
 * Mark utility pages noindex/follow in SEOPress output.
 *
 * @param array $directives Current SEOPress robots directives.
 * @return array
 */
function global360_filter_utility_page_seopress_robots( $directives ) {
	if ( ! global360_is_search_utility_page() ) {
		return $directives;
	}

	return array( 'noindex', 'follow' );
}
add_filter( 'seopress_titles_robots_attrs', 'global360_filter_utility_page_seopress_robots', 20 );

/**
 * Exclude utility pages from SEOPress XML sitemaps.
 *
 * @param array|null $url  Current sitemap URL payload.
 * @param WP_Post    $post Post being rendered.
 * @return array|null
 */
function global360_filter_utility_page_sitemap_url( $url, $post ) {
	return global360_is_search_utility_page( $post ) ? null : $url;
}
add_filter( 'seopress_sitemaps_single_url', 'global360_filter_utility_page_sitemap_url', 20, 2 );
