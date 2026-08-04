<?php

/**
 * Disable WordPress comments site-wide.
 */

defined('ABSPATH') || exit;


/**
 * Remove comment and trackback support from all post types.
 */
function pr360_disable_comment_support()
{
    foreach (get_post_types() as $post_type) {
        remove_post_type_support($post_type, 'comments');
        remove_post_type_support($post_type, 'trackbacks');
    }
}
add_action('init', 'pr360_disable_comment_support', 100);


/**
 * Force comments and pingbacks closed.
 */
add_filter('comments_open', '__return_false', 100);
add_filter('pings_open', '__return_false', 100);


/**
 * Hide any existing comments on the frontend.
 */
add_filter('comments_array', '__return_empty_array', 100);


/**
 * Ensure default comment settings stay closed for newly created content.
 */
add_filter('pre_option_default_comment_status', function () {
    return 'closed';
}, 100);
add_filter('pre_option_default_ping_status', function () {
    return 'closed';
}, 100);


/**
 * Block direct comment submissions.
 */
function pr360_block_comment_submissions()
{
    wp_die(
        esc_html__('Comments are disabled.', 'pr360'),
        esc_html__('Comments Disabled', 'pr360'),
        array('response' => 403)
    );
}
add_action('pre_comment_on_post', 'pr360_block_comment_submissions');


/**
 * Remove comment endpoints from the REST API.
 */
function pr360_remove_comment_rest_endpoints($endpoints)
{
    unset($endpoints['/wp/v2/comments']);
    unset($endpoints['/wp/v2/comments/(?P<id>[\\d]+)']);

    return $endpoints;
}
add_filter('rest_endpoints', 'pr360_remove_comment_rest_endpoints', 100);


/**
 * Disable XML-RPC pingback methods.
 */
function pr360_disable_xmlrpc_pingbacks($methods)
{
    unset($methods['pingback.ping']);
    unset($methods['pingback.extensions.getPingbacks']);

    return $methods;
}
add_filter('xmlrpc_methods', 'pr360_disable_xmlrpc_pingbacks', 100);


/**
 * Remove the Comments menu from WordPress admin.
 */
function pr360_remove_comments_admin_menu()
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'pr360_remove_comments_admin_menu');


/**
 * Prevent direct access to the comments admin screen.
 */
function pr360_block_comments_admin_screen()
{
    global $pagenow;

    if ('edit-comments.php' === $pagenow) {
        wp_safe_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'pr360_block_comments_admin_screen', 100);


/**
 * Remove Comments from the admin toolbar.
 */
function pr360_remove_comments_admin_bar($wp_admin_bar)
{
    $wp_admin_bar->remove_node('comments');
}
add_action('admin_bar_menu', 'pr360_remove_comments_admin_bar', 999);


/**
 * Remove the Recent Comments dashboard widget.
 */
function pr360_remove_comments_dashboard_widget()
{
    remove_meta_box(
        'dashboard_recent_comments',
        'dashboard',
        'normal'
    );
}
add_action('wp_dashboard_setup', 'pr360_remove_comments_dashboard_widget');


/**
 * Disable comment feeds.
 */
function pr360_disable_comment_feed()
{
    wp_die(
        esc_html__('Comment feeds are disabled.', 'pr360'),
        esc_html__('Comments Disabled', 'pr360'),
        array('response' => 404)
    );
}

add_action('do_feed_rss2_comments', 'pr360_disable_comment_feed', 1);
add_action('do_feed_atom_comments', 'pr360_disable_comment_feed', 1);


/**
 * Block all comment feed variants.
 */
function pr360_disable_all_comment_feeds()
{
    if (is_comment_feed()) {
        pr360_disable_comment_feed();
    }
}
add_action('template_redirect', 'pr360_disable_all_comment_feeds', 1);
