<?php
/**
 * Template Name: Linktree Landing
 * Template Post Type: page
 *
 * Displays a simplified landing page that surfaces core CTAs using
 * the theme's 360 Global Settings values.
 */

get_header();

$opts = get_option('360_global_settings', []);

$linktree_logo_id = isset($opts['linktree_logo_id']) ? (int) $opts['linktree_logo_id'] : 0;
if (! $linktree_logo_id) {
    $linktree_logo_id = isset($opts['header_logo_id']) ? (int) $opts['header_logo_id'] : 0;
}

$linktree_logo = '';
if ($linktree_logo_id) {
    $alt = get_post_meta($linktree_logo_id, '_wp_attachment_image_alt', true);
    if (! $alt) {
        $alt = get_bloginfo('name');
    }
    $alt = sanitize_text_field($alt);
    $linktree_logo = wp_get_attachment_image(
        $linktree_logo_id,
        'medium_large',
        false,
        [
            'class' => 'linktree-logo-img',
            'alt'   => $alt,
        ]
    );
}

$site_title   = trim(get_bloginfo('name'));
$linktree_h1  = $site_title ? $site_title . ' Linktree' : __('Linktree', 'cpt360');
$primary_hex  = isset($opts['primary_color']) ? sanitize_hex_color($opts['primary_color']) : '';
$inline_style = $primary_hex ? '--linktree-primary:' . $primary_hex . ';' : '';

$cta_links = [];
$assessment_id = function_exists('cpt360_get_assessment_id') ? cpt360_get_assessment_id() : '';
if ($assessment_id) {
    $cta_links[] = [
        'type'    => 'assessment',
        'label'   => __('Take Risk Assessment', 'cpt360'),
        'site_id' => $assessment_id,
    ];
}

$find_doctor_url = get_permalink(get_page_by_path('find-a-doctor'));
if (! $find_doctor_url) {
    $find_doctor_url = home_url('/find-a-doctor/');
}
$cta_links[] = [
    'type'  => 'link',
    'label' => __('Find a Doctor', 'cpt360'),
    'url'   => $find_doctor_url,
];

$cta_links[] = [
    'type'  => 'link',
    'label' => __('Visit Homepage', 'cpt360'),
    'url'   => home_url('/'),
];

$contact_phone = isset($opts['contact_phone']) ? trim($opts['contact_phone']) : '';
if ($contact_phone) {
    $cta_links[] = [
        'type'  => 'link',
        'label' => sprintf(__('Call %s', 'cpt360'), $contact_phone),
        'url'   => 'tel:' . preg_replace('/[^\d\+]/', '', $contact_phone),
    ];
}

$social_links = isset($opts['social_links']) && is_array($opts['social_links']) ? $opts['social_links'] : [];
$icon_map = [
    'facebook'  => 'fab fa-facebook-f',
    'instagram' => 'fab fa-instagram',
    'x'         => 'fab fa-x-twitter',
    'youtube'   => 'fab fa-youtube',
    'tiktok'    => 'fab fa-tiktok',
    'linkedin'  => 'fab fa-linkedin-in',
    'website'   => 'fas fa-globe',
];
?>

<main id="primary" class="site-main linktree-main">
    <div class="linktree-wrapper"<?php echo $inline_style ? ' style="' . esc_attr($inline_style) . '"' : ''; ?>>
        <div class="linktree-card">
            <?php if ($linktree_logo) : ?>
                <div class="linktree-logo">
                    <?php echo $linktree_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php endif; ?>

            <h1 class="linktree-title"><?php echo esc_html($linktree_h1); ?></h1>

            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php if (get_the_content()) : ?>
                        <div class="linktree-content">
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php endif; ?>

            <?php if ($cta_links) : ?>
                <ul class="linktree-cta-list">
                    <?php foreach ($cta_links as $cta) : ?>
                        <li>
                            <?php $type = isset($cta['type']) ? $cta['type'] : 'link'; ?>
                            <?php if ('assessment' === $type && !empty($cta['site_id'])) : ?>
                                <pr360-questionnaire
                                    url="wss://app.patientreach360.com/socket"
                                    site-id="<?php echo esc_attr($cta['site_id']); ?>">
                                    <?php echo esc_html($cta['label']); ?>
                                </pr360-questionnaire>
                            <?php else : ?>
                                <a class="linktree-button" href="<?php echo esc_url($cta['url']); ?>"<?php echo !empty($cta['target']) ? ' target="' . esc_attr($cta['target']) . '"' : ''; ?><?php echo !empty($cta['rel']) ? ' rel="' . esc_attr($cta['rel']) . '"' : ''; ?>>
                                    <?php echo esc_html($cta['label']); ?>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ($social_links) : ?>
                <ul class="linktree-social-list">
                    <?php foreach ($social_links as $row) :
                        $platform = isset($row['platform']) ? $row['platform'] : '';
                        $url      = isset($row['url']) ? $row['url'] : '';
                        if (! $platform || ! $url) {
                            continue;
                        }
                        $icon = isset($icon_map[$platform]) ? $icon_map[$platform] : 'fas fa-link';
                        ?>
                        <li>
                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">
                                <i class="<?php echo esc_attr($icon); ?>" aria-hidden="true"></i>
                                <span class="screen-reader-text"><?php echo esc_html(ucwords($platform)); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();