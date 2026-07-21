<?php

/**
 * Shared PR360 questionnaire renderer.
 *
 * Optional variables before include:
 * - $assessment_site_id (string)
 * - $assessment_url (string)
 * - $assessment_label (string)
 * - $assessment_inner_html (string, pre-escaped HTML)
 */

$assessment_site_id = isset($assessment_site_id) ? (string) $assessment_site_id : '';

if ($assessment_site_id === '' && function_exists('cpt360_get_assessment_id')) {
    $assessment_site_id = (string) cpt360_get_assessment_id();
}

if ($assessment_site_id === '') {
    return;
}

$assessment_url = isset($assessment_url) && $assessment_url !== ''
    ? (string) $assessment_url
    : 'wss://app.patientreach360.com/socket';

$assessment_label = isset($assessment_label) && $assessment_label !== ''
    ? (string) $assessment_label
    : 'Take Risk Assessment Now';

$assessment_inner_html = isset($assessment_inner_html) ? (string) $assessment_inner_html : '';

if ($assessment_inner_html === '') {
    $globals = get_option('360_global_settings', []);
    $custom_label = isset($globals['assessment_button_text']) ? trim((string) $globals['assessment_button_text']) : '';
    if ($custom_label !== '') {
        $assessment_label = $custom_label;
    }
}
?>
<pr360-questionnaire
    url="<?php echo esc_attr($assessment_url); ?>"
    site-id="<?php echo esc_attr($assessment_site_id); ?>">
    <?php if ($assessment_inner_html !== '') : ?>
        <?php echo $assessment_inner_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
        ?>
    <?php else : ?>
        <?php echo esc_html($assessment_label); ?>
    <?php endif; ?>
</pr360-questionnaire>