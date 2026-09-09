<?php

define('ABSPATH', __DIR__ . '/');

$privacy_actions = array(); $privacy_filters = array(); $privacy_is_admin = false; $privacy_is_author = false; $privacy_is_feed = false; $privacy_logged_in = false; $privacy_query_vars = array();
function add_action($hook, $callback, $priority = 10) { global $privacy_actions; $privacy_actions[$hook][$priority][] = $callback; }
function add_filter($hook, $callback, $priority = 10) { global $privacy_filters; $privacy_filters[$hook][$priority][] = $callback; }
function is_admin() { global $privacy_is_admin; return $privacy_is_admin; }
function is_author() { global $privacy_is_author; return $privacy_is_author; }
function is_feed() { global $privacy_is_feed; return $privacy_is_feed; }
function is_user_logged_in() { global $privacy_logged_in; return $privacy_logged_in; }
function get_query_var($key) { global $privacy_query_vars; return $privacy_query_vars[$key] ?? ''; }
function home_url($path = '/') { return 'https://example.test' . $path; }
function get_bloginfo($key) { return 'Example Clinic'; }
function sanitize_text_field($value) { return trim(strip_tags((string) $value)); }
function wp_strip_all_tags($value) { return strip_tags((string) $value); }
function __return_empty_string() { return ''; }

require_once dirname(__DIR__) . '/inc/privacy/public-author.php';

function privacy_expect($condition, $message) { if (! $condition) { throw new RuntimeException($message); } echo "PASS: {$message}\n"; }

privacy_expect(isset($privacy_actions['template_redirect'][0]), 'author redirects run before ordinary template redirects');
privacy_expect('https://example.test/' === global360_filter_public_author_link('https://example.test/author/private/'), 'frontend author links resolve to the homepage');
$privacy_is_admin = true; privacy_expect('https://example.test/author/private/' === global360_filter_public_author_link('https://example.test/author/private/'), 'admin author links remain unchanged'); $privacy_is_admin = false;
$oembed = global360_filter_oembed_public_identity(array('title' => 'Existing title', 'author_name' => 'Private Person', 'author_url' => 'https://example.test/author/private/', 'thumbnail_url' => 'image.jpg'));
privacy_expect('Example Clinic' === $oembed['author_name'] && 'https://example.test/' === $oembed['author_url'], 'oEmbed replaces personal author identity with site identity');
privacy_expect('Existing title' === $oembed['title'] && 'image.jpg' === $oembed['thumbnail_url'], 'oEmbed preserves title and image');
$social = global360_remove_seopress_article_author('<meta property="article:author" content="private"><meta property="article:publisher" content="organization">');
privacy_expect(false === strpos($social, 'article:author') && false !== strpos($social, 'article:publisher'), 'SEOPress article author is removed while publisher remains');
$sitemap = global360_remove_author_sitemap_entry('<sitemap><loc>https://example.test/post-sitemap1.xml</loc></sitemap><sitemap><loc>https://example.test/author.xml</loc></sitemap>');
privacy_expect(false === strpos($sitemap, 'author.xml') && false !== strpos($sitemap, 'post-sitemap1.xml'), 'only the author sitemap index entry is removed');
$endpoints = array('/wp/v2/users' => array(), '/wp/v2/users/(?P<id>[\d]+)' => array(), '/wp/v2/users/me' => array(), '/wp/v2/posts' => array());
privacy_expect(array('/wp/v2/posts') === array_keys(global360_hide_public_rest_users($endpoints)), 'anonymous REST user routes are removed without affecting other routes');
$privacy_logged_in = true; privacy_expect($endpoints === global360_hide_public_rest_users($endpoints), 'authenticated REST user routes remain available');
$privacy_is_feed = true; privacy_expect('Example Clinic' === global360_filter_feed_author_name('Private Person'), 'feeds use organization identity');
$privacy_is_feed = false; privacy_expect('Private Person' === global360_filter_feed_author_name('Private Person'), 'non-feed author values are not globally rewritten');
$privacy_is_author = true; privacy_expect(global360_is_public_author_request(), 'WordPress author archives are recognized'); $privacy_is_author = false;
$privacy_query_vars['seopress_author'] = '1'; privacy_expect(global360_is_public_author_request(), 'SEOPress author sitemap requests are recognized');

$templates = file_get_contents(dirname(__DIR__) . '/template-parts/content.php') . file_get_contents(dirname(__DIR__) . '/template-parts/content-search.php');
privacy_expect(false === strpos($templates, 'global_360_theme_posted_by'), 'post and search templates expose no author byline or archive link');
$template_tags = file_get_contents(dirname(__DIR__) . '/inc/template-tags.php');
privacy_expect(false === strpos($template_tags, 'get_author_posts_url'), 'compatibility template tag cannot emit an author archive URL');
$clinic_cta = file_get_contents(dirname(__DIR__) . '/clinic-partials/clinic-cta.php');
privacy_expect(false === strpos($clinic_cta, '__DIR__') || false === strpos($clinic_cta, 'looking for V2 button'), 'clinic CTA emits no absolute filesystem debug comment');

echo "Global 360 public author privacy tests passed.\n";
