<?php
/**
 * Template for displaying single doctor posts
 * URL: /doctors/doctor-name/
 */

get_header();

if (have_posts()) : while (have_posts()) : the_post();

$doctor_id = get_the_ID();

if (!function_exists('extract_city_from_address')) {
    function extract_city_from_address($address)
    {
        if (!function_exists('parse_address_for_display')) {
            return '';
        }

        $parsed = parse_address_for_display($address);
        return isset($parsed['city']) ? (string) $parsed['city'] : '';
    }
}

if (!function_exists('normalize_state_abbreviation')) {
    function normalize_state_abbreviation($text)
    {
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }

        return preg_replace_callback(
            '/\b([A-Za-z]{2})(?=\s+\d{5}(?:-\d{4})?\b)/',
            function ($matches) {
                return strtoupper($matches[1]);
            },
            $text
        );
    }
}

if (!function_exists('parse_address_for_display')) {
    function parse_address_for_display($address)
    {
        $address = normalize_state_abbreviation($address);
        if ($address === '') {
            return [
                'city' => '',
                'street' => '',
            ];
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', $address)), function ($part) {
            return $part !== '';
        }));

        if (empty($parts)) {
            return [
                'city' => '',
                'street' => $address,
            ];
        }

        $city = '';
        $city_index = -1;

        for ($i = count($parts) - 1; $i >= 0; $i--) {
            $segment = preg_replace('/\s+/', ' ', $parts[$i]);

            if (preg_match('/^(.+?)\s+[A-Za-z]{2}\s+\d{5}(?:-\d{4})?$/', $segment, $matches)) {
                $city = trim((string) $matches[1]);
                $city_index = $i;
                break;
            }

            if (preg_match('/^(.+?)\s+[A-Za-z]{2}$/', $segment, $matches)) {
                $city = trim((string) $matches[1]);
                $city_index = $i;
                break;
            }

            if (
                $i < (count($parts) - 1)
                && preg_match('/^[A-Za-z]{2}\s+\d{5}(?:-\d{4})?$/', preg_replace('/\s+/', ' ', $parts[$i + 1]))
            ) {
                $city = trim((string) $segment);
                $city_index = $i;
                break;
            }
        }

        if ($city_index === -1 && count($parts) >= 2) {
            $candidate = preg_replace('/\s+/', ' ', $parts[count($parts) - 1]);
            if (!preg_match('/\d{3,}/', $candidate)) {
                $city = trim((string) $candidate);
                $city_index = count($parts) - 1;
            }
        }

        $street = '';
        if ($city_index > 0) {
            $street = trim(implode(', ', array_slice($parts, 0, $city_index)));
        }
        if ($street === '' && !empty($parts[0])) {
            $street = trim((string) $parts[0]);
        }

        return [
            'city' => $city,
            'street' => $street,
        ];
    }
}

// Get doctor data
$doctor_name = get_post_meta($doctor_id, 'doctor_name', true) ?: get_the_title();
$doctor_title = get_post_meta($doctor_id, 'doctor_title', true);
$doctor_bio = get_post_meta($doctor_id, 'doctor_bio', true);

// Get doctor photo
$photo_id = get_post_meta($doctor_id, '_doctor_photo_id', true);
if ($photo_id) {
    $photo_url = wp_get_attachment_image_url($photo_id, 'medium');
} else {
    // Fallback to file-based image with multiple extension support
    $slug = get_post_field('post_name', $doctor_id);
    $base_path = get_template_directory() . '/assets/doctor-images/';
    $base_url = get_template_directory_uri() . '/assets/doctor-images/';
    $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    $photo_url = '';

    foreach ($extensions as $ext) {
        $file_path = $base_path . $slug . '.' . $ext;
        if (file_exists($file_path)) {
            $photo_url = $base_url . $slug . '.' . $ext;
            break;
        }
    }
}

// Get associated clinics
$clinic_ids = (array) get_post_meta($doctor_id, 'clinic_id', true);
$clinics = [];
if (!empty($clinic_ids)) {
    $clinics = get_posts([
        'post_type' => 'clinic',
        'include' => $clinic_ids,
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);
}

$opts = get_option('360_global_settings', []);
$primary_condition = isset($opts['primary_condition']) ? trim((string) $opts['primary_condition']) : '';
$related_conditions = isset($opts['related_conditions']) ? trim((string) $opts['related_conditions']) : '';
$primary_treatment = isset($opts['primary_treatment']) ? trim((string) $opts['primary_treatment']) : '';
$related_treatments = isset($opts['related_treatments']) ? trim((string) $opts['related_treatments']) : '';
$condition_page_id = isset($opts['condition_page']) ? intval($opts['condition_page']) : 0;
$treatment_page_id = isset($opts['treatment_page']) ? intval($opts['treatment_page']) : 0;

$condition_url = $condition_page_id ? get_permalink($condition_page_id) : '';
$treatment_url = $treatment_page_id ? get_permalink($treatment_page_id) : '';

$related_condition_items = [];
if ($related_conditions !== '') {
    $related_parts = explode(',', $related_conditions);
    foreach ($related_parts as $condition) {
        $condition = trim((string) $condition);
        if ($condition !== '') {
            $related_condition_items[] = $condition;
        }
    }
    $related_condition_items = array_values(array_unique($related_condition_items));
}

?>

<style>
    .doctor-learn-more {
        margin: 12px 0 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .doctor-learn-link {
        display: inline-block;
        color: var(--cpt360-primary);
        text-decoration: none;
        font-weight: 600;
    }

    .doctor-learn-link:visited {
        color: var(--cpt360-primary);
    }

    .doctor-learn-link:hover,
    .doctor-learn-link:focus {
        text-decoration: underline;
    }

    .doctor-practice-locations .clinic-address-list {
        margin-left: 0;
        padding-left: 0;
    }
</style>

<main id="primary" class="site-main">
    <div class="sm_hero">
        <h1><?php echo esc_html($doctor_name); ?></h1>
    </div>

    <div class="max_width_content_body">
        <div class="doctor-info-container">
            <div class="doctor-details">
                <div class="doctor-left-column">
                    <?php if ($photo_url): ?>
                        <div class="doctor-photo">
                            <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($doctor_name); ?>" />
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="doctor-right-column">
                    <div class="doctor-bio">
                        <h2>About <?php echo esc_html($doctor_name); ?></h2>
                        <?php if ($doctor_title): ?>
                            <p class="doctor-title"><?php echo esc_html($doctor_title); ?></p>
                        <?php endif; ?>
                        <div class="bio-content">
                            <?php 
                            if ($doctor_bio && trim($doctor_bio)) {
                                echo '<p>' . nl2br(htmlspecialchars($doctor_bio)) . '</p>';
                            } else {
                                echo '<p><em>Biography coming soon.</em></p>';
                            }
                            ?>
                        </div>
                    </div>

                    <?php if ($primary_condition): ?>
                        <section class="doctor-conditions">
                            <h2>Conditions Treated</h2>
                            <p>
                                Dr <?php echo esc_html($doctor_name); ?> treats patients suffering from
                                <?php echo esc_html($primary_condition); ?>
                                <?php if (!empty($related_condition_items)): ?>
                                    and related conditions such as <?php echo esc_html(implode(', ', $related_condition_items)); ?>.
                                <?php else: ?>
                                    and related chronic pain conditions.
                                <?php endif; ?>
                            </p>
                        </section>
                    <?php endif; ?>

                    <?php
                    $treatment_items = [];
                    if ($primary_treatment !== '') {
                        $treatment_items[] = $primary_treatment;
                    }

                    if ($related_treatments !== '') {
                        $related = explode(',', $related_treatments);
                        foreach ($related as $treatment) {
                            $treatment = trim((string) $treatment);
                            if ($treatment !== '') {
                                $treatment_items[] = $treatment;
                            }
                        }
                    }

                    $treatment_items = array_values(array_unique($treatment_items));
                    ?>

                    <?php if (!empty($treatment_items)): ?>
                        <section class="doctor-treatments">
                            <h2>Treatments Offered</h2>
                            <ul style="margin-left: 0;">
                                <?php foreach ($treatment_items as $treatment): ?>
                                    <li><?php echo esc_html($treatment); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endif; ?>

                    <?php if ($condition_url || $treatment_url): ?>
                        <div class="doctor-learn-more">
                            <?php if ($condition_url && $primary_condition): ?>
                                <a class="doctor-learn-link" href="<?php echo esc_url($condition_url); ?>">
                                    Learn more about <?php echo esc_html($primary_condition); ?> →
                                </a>
                            <?php endif; ?>

                            <?php if ($treatment_url && $primary_treatment): ?>
                                <a class="doctor-learn-link" href="<?php echo esc_url($treatment_url); ?>">
                                    Learn how <?php echo esc_html($primary_treatment); ?> works →
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($clinics)): ?>
                        <div class="doctor-practice-locations">
                            <h2>Practice Locations</h2>
                            <div class="practice-locations-list">
                                <?php foreach ($clinics as $clinic): ?>
                                    <div class="practice-location">
                                        <h3><a href="<?php echo esc_url(get_permalink($clinic->ID)); ?>"><?php echo esc_html($clinic->post_title); ?></a></h3>
                                        
                                        <?php
                                        // Get clinic addresses
                                        $addresses = get_post_meta($clinic->ID, 'clinic_addresses', true);
                                        $cities = [];

                                        if (is_array($addresses) && !empty($addresses)) {
                                            foreach ($addresses as $address) {
                                                $full_address = '';
                                                $street_only = '';
                                                $city_name = '';

                                                if (is_string($address)) {
                                                    $full_address = normalize_state_abbreviation($address);
                                                    $parsed_address = parse_address_for_display($full_address);
                                                    $street_only = isset($parsed_address['street']) ? trim((string) $parsed_address['street']) : '';
                                                    $city_name = isset($parsed_address['city']) ? trim((string) $parsed_address['city']) : '';
                                                } elseif (is_array($address)) {
                                                    $street = isset($address['street']) ? trim((string) $address['street']) : '';
                                                    $city = isset($address['city']) ? trim((string) $address['city']) : '';
                                                    $state = isset($address['state']) ? strtoupper(trim((string) $address['state'])) : '';
                                                    $zip = isset($address['zip']) ? trim((string) $address['zip']) : '';

                                                    $street_only = $street;
                                                    $city_name = $city;

                                                    if ($street !== '' && $city !== '') {
                                                        $full_address = $street . ', ' . $city;
                                                        if ($state !== '') {
                                                            $full_address .= ' ' . $state;
                                                        }
                                                        if ($zip !== '') {
                                                            $full_address .= ' ' . $zip;
                                                        }
                                                    } elseif ($street !== '') {
                                                        $full_address = $street;
                                                    }
                                                }

                                                if ($full_address === '') {
                                                    continue;
                                                }

                                                if ($city_name === '') {
                                                    $city_name = extract_city_from_address($full_address);
                                                }
                                                if ($city_name === '') {
                                                    continue;
                                                }

                                                if (!isset($cities[$city_name])) {
                                                    $cities[$city_name] = [];
                                                }

                                                $address_for_list = $street_only !== '' ? $street_only : $full_address;
                                                $cities[$city_name][] = $address_for_list;
                                            }

                                            foreach ($cities as $city_key => $city_addresses) {
                                                $cities[$city_key] = array_values(array_unique(array_filter(array_map('trim', $city_addresses))));
                                            }
                                        }

                                        if (is_array($addresses) && !empty($addresses)):
                                        ?>
                                            <div class="clinic-addresses">
                                                <?php if (!empty($cities)): ?>
                                                    <?php foreach ($cities as $city => $city_addresses): ?>
                                                        <div class="clinic-city-block">
                                                            <h4><?php echo esc_html($city); ?></h4>
                                                            <ul class="clinic-address-list" style="margin-left: 0; padding-left: 0;">
                                                                <?php foreach ($city_addresses as $address_line): ?>
                                                                    <li><?php echo esc_html($address_line); ?></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <?php foreach ($addresses as $address): ?>
                                                        <?php
                                                        $fallback_line = is_array($address)
                                                            ? (isset($address['street']) ? $address['street'] : '')
                                                            : (string) $address;
                                                        ?>
                                                        <p><?php echo esc_html(normalize_state_abbreviation($fallback_line)); ?></p>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php
                                        // Get clinic phone
                                        $phone = get_post_meta($clinic->ID, 'clinic_phone', true);
                                        if ($phone):
                                        ?>
                                            <div class="clinic-phone">
                                                <strong>Phone:</strong> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                                            </div>
                                        <?php endif; ?>

                                        <?php
                                        // Get clinic website
                                        $website = get_post_meta($clinic->ID, 'clinic_website', true);
                                        if ($website):
                                        ?>
                                            <div class="clinic-website">
                                                <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener">Visit Website</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="doctor-practice-locations">
                            <h2>Practice Locations</h2>
                            <p><em>No practice locations found.</em></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Back navigation -->
            <div class="doctor-navigation">
                <?php 
                // Determine back link based on number of clinics
                if (count($clinics) === 1) {
                    // If doctor has only one clinic, go back to that clinic
                    $back_url = get_permalink($clinics[0]->ID);
                    $back_text = "← Back to Clinic Page";
                } else if (count($clinics) > 1) {
                    // If multiple clinics, go to the first one
                    $back_url = get_permalink($clinics[0]->ID);
                    $back_text = "← Back to Clinic Page";
                } else {
                    // If no clinics found, go to find-a-doctor
                    $back_url = "/find-a-doctor/";
                    $back_text = "← Back to Find a Doctor";
                }
                ?>
                <a href="<?php echo esc_url($back_url); ?>" class="back-button"><?php echo $back_text; ?></a>
            </div>
        </div>
    </div>
</main>

<?php endwhile; endif; ?>

<?php get_footer(); ?>