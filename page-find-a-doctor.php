<?php
/*
Template Name: Find a Doctor
*/

get_header();
?>

	<main id="primary" class="site-main">

        <?php
        while ( have_posts() ) :
            the_post();

            // Get the page content and inject the grid after the hero
            $content = apply_filters( 'the_content', get_the_content() );
            // Build the grid markup
            ob_start();
            echo '<div class="body_heading">';
            echo '<h2>Click Your State Below</h2>';
            echo '</div>';
            echo '<div class="state_grid_wrapper max_width_content_body">';
            $states = [
                'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas',
                'CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware',
                'FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho',
                'IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas',
                'KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland',
                'MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi',
                'MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada',
                'NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York',
                'NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma',
                'OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina',
                'SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah',
                'VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia',
                'WI'=>'Wisconsin','WY'=>'Wyoming',
            ];
            $default_clinic_url = '/clinics/interventional-radiology-institute/';
            $svg_base = get_template_directory_uri() . '/assets/state_svg/';
            echo '<ul class="state-grid">';
            foreach ($states as $abbr => $name) {
                $clinics = get_posts([
                    'post_type' => 'clinic',
                    'posts_per_page' => 1,
                    'meta_key' => '_cpt360_clinic_state',
                    'meta_value' => $abbr,
                ]);
                if ($clinics) {
                    $state_slug = strtolower(str_replace(' ', '-', $name));
                    $link = '/find-a-doctor/' . $state_slug . '/';
                } else {
                    $link = $default_clinic_url;
                }
                $svg_filename = str_replace(' ', '_', $name) . '.svg';
                $svg_file = $svg_base . $svg_filename;
                echo '<li>
                    <a href="' . esc_url($link) . '">
                        <div class="state-icon" style="--mask-url:url(\'' . esc_url($svg_file) . '\')"></div>
                        <span>' . esc_html($name) . '</span>
                    </a>
                </li>';
            }
            echo '</ul>';
            echo '</div>';
            $grid = ob_get_clean();
            echo $grid . $content;

        endwhile; // End of the loop.
        
        ?>
	</main><!-- #main -->

<?php

get_footer();
