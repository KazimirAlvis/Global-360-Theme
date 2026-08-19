<?php

/**
 * Template part: Display all Doctors for the current Clinic
 * Save this as includes/clinic-doctors.php in your plugin.
 */

if (!function_exists('cpt360_render_clinic_doctors')) {
	function cpt360_render_clinic_doctors()
	{
    // 1) Get the current clinic ID
    $clinic_id = get_the_ID();
    if (! $clinic_id) {
        return;
    }

    $clinic_view = function_exists('global360_theme_clinic') ? global360_theme_clinic($clinic_id) : null;
    $doctor_ids = is_array($clinic_view) ? (array) ($clinic_view['doctor_ids'] ?? array()) : array();

    $clinic_doctors = empty($doctor_ids) ? array() : get_posts([
        'post_type'      => 'doctor',
        'post_status'    => 'publish',
        'post__in'       => array_map('absint', $doctor_ids),
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ]);

    // Output doctors
    if ($clinic_doctors) {
        echo '<section class="clinic-doctors">';
        echo '<h2>Our Doctors</h2>';
        echo '<div class="doctors-grid">';
        foreach ($clinic_doctors as $doc) {
            $post_id  = $doc->ID;
            $doctor_view = function_exists('global360_theme_doctor') ? global360_theme_doctor($post_id) : null;
            $name     = is_array($doctor_view) ? ($doctor_view['name'] ?? get_the_title($post_id)) : get_the_title($post_id);
            $title    = is_array($doctor_view) ? ($doctor_view['title'] ?? '') : '';
            $photo_id = is_array($doctor_view) ? ($doctor_view['photo_attachment_id'] ?? 0) : 0;
            $doctor_url = get_permalink($post_id);

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
                    '<a href="%3$s"><div class="doctor-photo"><img src="%1$s" alt="%2$s" /></div></a>',
                    esc_url($photo_url),
                    esc_attr($name),
                    esc_url($doctor_url)
                );
            }
            printf('<h3 class="doctor-name"><a href="%2$s">%1$s</a></h3>', esc_html($name), esc_url($doctor_url));
            if ($title) {
                printf('<p class="doctor-title">%s</p>', esc_html($title));
            }
            echo '</div>'; // .doctor-profile
        }
        echo '</div>'; // .doctors-grid
        echo '</section>';
    }
	}
}
