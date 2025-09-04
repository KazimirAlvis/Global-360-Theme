<?php

/**
 * Template part: Display all Doctors for the current Clinic
 * Save this as includes/clinic-doctors.php in your plugin.
 */



function cpt360_render_clinic_doctors()
{
    echo '<!-- cpt360_render_clinic_doctors CALLED -->';
    // 1) Get the current clinic ID
    $clinic_id = get_the_ID();
    if (! $clinic_id) {
        return;
    }

    $all_doctors = get_posts([
        'post_type' => 'doctor',
        'posts_per_page' => -1,
    ]);

    $clinic_doctors = [];
    foreach ($all_doctors as $doc) {
        $meta = get_post_meta($doc->ID, 'clinic_id', true);
        if (is_array($meta) && in_array($clinic_id, $meta)) {
            $clinic_doctors[] = $doc;
        } elseif ($meta == $clinic_id) {
            $clinic_doctors[] = $doc;
        }
    }

    // Output doctors
    if ($clinic_doctors) {
        echo '<section class="clinic-doctors">';
        echo '<h2>Our Doctors</h2>';
        echo '<div class="doctors-grid">';
        foreach ($clinic_doctors as $doc) {
            $post_id  = $doc->ID;
            $name     = get_post_meta($post_id, 'doctor_name',  true) ?: get_the_title($post_id);
            $title    = get_post_meta($post_id, 'doctor_title', true);
            $bio      = get_post_meta($post_id, 'doctor_bio',   true);
            $photo_id = get_post_meta($post_id, '_doctor_photo_id', true);

            if ($photo_id) {
                $photo_url = wp_get_attachment_image_url($photo_id, 'medium');
            } else {

                $slug       = get_post_field('post_name', $post_id);
                $base_path  = get_template_directory() . '/assets/doctor-images/';
                $base_url   = get_template_directory_uri() . '/assets/doctor-images/';
                $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
                $photo_url  = '';

                foreach ($extensions as $ext) {
                    $file_path = $base_path . $slug . '.' . $ext;
                    if (file_exists($file_path)) {
                        $photo_url = $base_url . $slug . '.' . $ext;
                        break;
                    }
                }
            }

            echo '<div class="doctor-profile">';
            if ($photo_url) {
                printf(
                    '<div class="doctor-photo"><img src="%1$s" alt="%2$s" /></div>',
                    esc_url($photo_url),
                    esc_attr($name)
                );
            }
            printf('<h3 class="doctor-name">%s</h3>', esc_html($name));
            if ($title) {
                printf('<p class="doctor-title">%s</p>', esc_html($title));
            }
            if ($bio) {
                echo '<div class="doctor-bio">' . wpautop(esc_html($bio)) . '</div>';
            }
            echo '</div>'; // .doctor-profile
        }
        echo '</div>'; // .doctors-grid
        echo '</section>';
    }
}
