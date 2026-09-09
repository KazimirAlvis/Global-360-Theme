<?php
/**
 * Public author privacy controls.
 *
 * WordPress post authorship remains available to administrators and editors.
 * Public-facing identity uses the configured site/organization instead.
 *
 * @package Global-360-Theme
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('global360_public_organization_name')) {
	function global360_public_organization_name()
	{
		$context = function_exists('global360_theme_site_context') ? global360_theme_site_context() : array();
		$name = is_array($context) ? (string) ($context['site_name'] ?? '') : '';
		$name = sanitize_text_field(wp_strip_all_tags($name));

		return '' !== $name ? $name : sanitize_text_field(wp_strip_all_tags((string) get_bloginfo('name')));
	}
}

if (! function_exists('global360_is_public_author_request')) {
	function global360_is_public_author_request()
	{
		return is_author() || (string) get_query_var('seopress_author') !== '';
	}
}

if (! function_exists('global360_redirect_public_author_archives')) {
	function global360_redirect_public_author_archives()
	{
		if (! global360_is_public_author_request()) {
			return;
		}

		wp_safe_redirect(home_url('/'), 301, 'Global 360 Theme');
		exit;
	}
}
add_action('template_redirect', 'global360_redirect_public_author_archives', 0);

if (! function_exists('global360_filter_public_author_link')) {
	function global360_filter_public_author_link($url)
	{
		return is_admin() ? $url : home_url('/');
	}
}
add_filter('author_link', 'global360_filter_public_author_link');

if (! function_exists('global360_filter_oembed_public_identity')) {
	function global360_filter_oembed_public_identity($data)
	{
		if (! is_array($data)) {
			return $data;
		}

		$data['author_name'] = global360_public_organization_name();
		$data['author_url'] = home_url('/');

		return $data;
	}
}
add_filter('oembed_response_data', 'global360_filter_oembed_public_identity', 999);

if (! function_exists('global360_remove_seopress_article_author')) {
	function global360_remove_seopress_article_author($html)
	{
		return preg_replace('/<meta\s+property=["\']article:author["\'][^>]*>\s*/i', '', (string) $html);
	}
}
add_filter('seopress_social_og_author', 'global360_remove_seopress_article_author', 999);
add_filter('seopress_social_twitter_card_creator', '__return_empty_string', 999);

if (! function_exists('global360_remove_author_sitemap_entry')) {
	function global360_remove_author_sitemap_entry($xml)
	{
		return preg_replace('#\s*<sitemap>\s*<loc>[^<]*/author\.xml</loc>\s*</sitemap>#i', '', (string) $xml);
	}
}
add_filter('seopress_sitemaps_xml_index_item', 'global360_remove_author_sitemap_entry', 999);
add_filter('seopress_sitemaps_xml_author', '__return_empty_string', 999);

if (! function_exists('global360_disable_public_user_sitemap')) {
	function global360_disable_public_user_sitemap($provider, $name)
	{
		return 'users' === $name ? false : $provider;
	}
}
add_filter('wp_sitemaps_add_provider', 'global360_disable_public_user_sitemap', 10, 2);

if (! function_exists('global360_hide_public_rest_users')) {
	function global360_hide_public_rest_users($endpoints)
	{
		if (is_user_logged_in()) {
			return $endpoints;
		}

		unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'], $endpoints['/wp/v2/users/me']);

		return $endpoints;
	}
}
add_filter('rest_endpoints', 'global360_hide_public_rest_users');

if (! function_exists('global360_filter_feed_author_name')) {
	function global360_filter_feed_author_name($name)
	{
		return is_feed() ? global360_public_organization_name() : $name;
	}
}
add_filter('the_author', 'global360_filter_feed_author_name');
