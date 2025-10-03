<?php
/**
 * Template for displaying single doctor posts
 * URL: /doctors/doctor-name/
 */

get_header();

if (have_posts()) : while (have_posts()) : the_post();

$doctor_id = get_the_ID();

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

?>

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
                                        if (is_array($addresses) && !empty($addresses)):
                                        ?>
                                            <div class="clinic-addresses">
                                                <?php foreach ($addresses as $address): ?>
                                                    <div class="clinic-address">
                                                        <?php 
                                                        $full_address = $address['street'];
                                                        if (!empty($address['city'])) {
                                                            $full_address .= ', ' . $address['city'];
                                                        }
                                                        if (!empty($address['state'])) {
                                                            $full_address .= ', ' . $address['state'];
                                                        }
                                                        if (!empty($address['zip'])) {
                                                            $full_address .= ' ' . $address['zip'];
                                                        }
                                                        $maps_url = 'https://maps.google.com/maps?q=' . urlencode($full_address);
                                                        ?>
                                                        <p><a href="<?php echo esc_url($maps_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($address['street']); ?></a></p>
                                                    </div>
                                                <?php endforeach; ?>
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