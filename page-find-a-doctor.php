<?php
/*
Template Name: Find a Doctor
*/

get_header();
?>

<main id="primary" class="site-main">

    <?php
    while (have_posts()) :
        the_post();

        $globals = global360_theme_site_context();
        $global_assessment_id = isset($globals['assessment_id']) ? sanitize_text_field($globals['assessment_id']) : '';

        // Get the page content and inject the grid after the hero
        $content = apply_filters('the_content', get_the_content());
        // Build the grid markup
        ob_start();
        echo '<div class="body_heading">';
        echo '<h2>Click Your State Below</h2>';
        echo '</div>';
        echo '<div class="state_grid_wrapper max_width_content_body">';
        $states = function_exists('global360_platform') ? global360_platform()->states()->all() : [
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
            'DC' => 'District of Columbia',
        ];
        $default_clinic_url = '/clinics/interventional-radiology-institute/';
        $svg_child_dir = trailingslashit(get_stylesheet_directory()) . 'assets/state_svg/';
        $svg_child_uri = trailingslashit(get_stylesheet_directory_uri()) . 'assets/state_svg/';
        $svg_parent_dir = trailingslashit(get_template_directory()) . 'assets/state_svg/';
        $svg_parent_uri = trailingslashit(get_template_directory_uri()) . 'assets/state_svg/';
		$available_states = array();
		$clinic_posts = get_posts(array(
			'post_type'              => 'clinic',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		));
		foreach ($clinic_posts as $clinic_post) {
			$clinic_view = global360_theme_clinic((int) $clinic_post->ID);
			foreach ((array) ($clinic_view['state_codes'] ?? array()) as $clinic_state) {
				$available_states[strtoupper((string) $clinic_state)] = true;
			}
		}
        echo '<ul class="state-grid">';
        foreach ($states as $abbr => $name) {
			$has_clinic = isset($available_states[$abbr]);
            $svg_filename = str_replace(' ', '_', $name) . '.svg';
            $svg_path = $svg_child_dir . $svg_filename;
            if (! file_exists($svg_path)) {
                $svg_path = $svg_parent_dir . $svg_filename;
            }

            if (file_exists($svg_path)) {
                $svg_file = (strpos($svg_path, $svg_child_dir) === 0)
                    ? $svg_child_uri . $svg_filename
                    : $svg_parent_uri . $svg_filename;
            } else {
                $svg_file = '';
            }
            echo '<li>';
            if ($has_clinic) {
                $state_slug = strtolower(str_replace(' ', '-', $name));
                $link = '/find-a-doctor/' . $state_slug . '/';
                echo '<a href="' . esc_url($link) . '">
                            <div class="state-icon" style="--mask-url:url(\'' . esc_url($svg_file) . '\')"></div>
                            <span>' . esc_html($name) . '</span>
                        </a>';
            } elseif ($global_assessment_id) {
                $assessment_site_id = $global_assessment_id;
                $assessment_inner_html = '<div class="state-icon" style="--mask-url:url(\'' . esc_url($svg_file) . '\')"></div>'
                    . '<span>' . esc_html($name) . '</span>';
                require get_template_directory() . '/clinic-partials/assessment-questionnaire.php';
            } else {
                echo '<a href="' . esc_url($default_clinic_url) . '">
                            <div class="state-icon" style="--mask-url:url(\'' . esc_url($svg_file) . '\')"></div>
                            <span>' . esc_html($name) . '</span>
                        </a>';
            }
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
        $grid = ob_get_clean();

        $condition_page_id = (int) get_option('primary_condition_page');
        if (! $condition_page_id && isset($globals['primary_condition_page'])) {
            $condition_page_id = (int) $globals['primary_condition_page'];
        }
        if (! $condition_page_id && isset($globals['condition_page'])) {
            $condition_page_id = (int) $globals['condition_page'];
        }
        $condition_url = $condition_page_id ? get_permalink($condition_page_id) : '';

        $treatment_page_id = (int) get_option('primary_treatment_page');
        if (! $treatment_page_id && isset($globals['primary_treatment_page'])) {
            $treatment_page_id = (int) $globals['primary_treatment_page'];
        }
        if (! $treatment_page_id && isset($globals['treatment_page'])) {
            $treatment_page_id = (int) $globals['treatment_page'];
        }
        $treatment_url = $treatment_page_id ? get_permalink($treatment_page_id) : '';

        $faq_page = get_page_by_path('faq');
        $faq_url = ($faq_page instanceof WP_Post) ? get_permalink($faq_page) : '';

        $posts_page_id = (int) get_option('page_for_posts');
        $blog_url = $posts_page_id ? get_permalink($posts_page_id) : '';

        $directory_links = array();
        if ($condition_url) {
            $directory_links[] = '<li><a href="' . esc_url($condition_url) . '">Learn more about this condition</a></li>';
        }
        if ($treatment_url) {
            $directory_links[] = '<li><a href="' . esc_url($treatment_url) . '">Learn about available treatment options</a></li>';
        }
        if ($faq_url) {
            $directory_links[] = '<li><a href="' . esc_url($faq_url) . '">Frequently asked questions</a></li>';
        }
        if ($blog_url) {
            $directory_links[] = '<li><a href="' . esc_url($blog_url) . '">Read our latest articles</a></li>';
        }

        echo $content;
        echo $grid;

        if (! empty($directory_links)) {
            echo '<section class="doctor-directory-learn-more max_width_content_body" style="order: 4;">';
            echo '<h2>Learn More</h2>';
            echo '<ul class="doctor-directory-links">' . implode('', $directory_links) . '</ul>';
            echo '</section>';
        }

    endwhile; // End of the loop.

    ?>
</main><!-- #main -->

<?php

get_footer();
