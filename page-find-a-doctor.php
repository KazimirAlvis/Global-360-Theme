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

            $globals = get_option( '360_global_settings', [] );
            $global_assessment_id = isset( $globals['assessment_id'] ) ? sanitize_text_field( $globals['assessment_id'] ) : '';

            // Get the page content and inject the grid after the hero
            $content = apply_filters( 'the_content', get_the_content() );
            // Build the grid markup
            ob_start();
            if ( $global_assessment_id ) {
                echo '<style>';
                // Prevent any single item (including PR360) from expanding the grid tracks.
                echo '.state-grid{grid-template-columns:repeat(8,minmax(0,1fr));}';
                echo '@media (max-width: 1200px){.state-grid{grid-template-columns:repeat(4,minmax(0,1fr));}}';
                echo '@media (max-width: 550px){.state-grid{grid-template-columns:repeat(3,minmax(0,1fr));}}';
                echo '.state-grid>li{min-width:0;}';
                echo '.state-grid>li>a{display:flex;flex-direction:column;align-items:center;width:100%;min-width:0;}';

                // Make the PR360 tile behave like the <a> tile (full-cell, no default button chrome).
                echo '.state-grid li pr360-questionnaire{display:block;width:100%;height:100%;min-width:0;}';
                echo '.state-grid pr360-questionnaire::part(begin-button){background:transparent !important;border:0 !important;box-shadow:none !important;outline:none !important;padding:0 !important;margin:0 !important;width:100% !important;height:100% !important;min-width:0 !important;max-width:100% !important;cursor:pointer !important;display:flex !important;flex-direction:column !important;align-items:center !important;justify-content:flex-start !important;font:inherit !important;color:inherit !important;text-align:center !important;box-sizing:border-box !important;}';
                echo '</style>';
            }
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
            $svg_child_dir = trailingslashit( get_stylesheet_directory() ) . 'assets/state_svg/';
            $svg_child_uri = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/state_svg/';
            $svg_parent_dir = trailingslashit( get_template_directory() ) . 'assets/state_svg/';
            $svg_parent_uri = trailingslashit( get_template_directory_uri() ) . 'assets/state_svg/';
            echo '<ul class="state-grid">';
            foreach ($states as $abbr => $name) {
                $clinics = get_posts([
                    'post_type' => 'clinic',
                    'posts_per_page' => 1,
                    'meta_key' => '_cpt360_clinic_state',
                    'meta_value' => $abbr,
                ]);
                $has_clinic = ! empty( $clinics );
                $svg_filename = str_replace(' ', '_', $name) . '.svg';
                $svg_path = $svg_child_dir . $svg_filename;
                if ( ! file_exists( $svg_path ) ) {
                    $svg_path = $svg_parent_dir . $svg_filename;
                }

                if ( file_exists( $svg_path ) ) {
                    $svg_file = ( strpos( $svg_path, $svg_child_dir ) === 0 )
                        ? $svg_child_uri . $svg_filename
                        : $svg_parent_uri . $svg_filename;
                } else {
                    $svg_file = '';
                }
                echo '<li>';
                if ( $has_clinic ) {
                    $state_slug = strtolower(str_replace(' ', '-', $name));
                    $link = '/find-a-doctor/' . $state_slug . '/';
                    echo '<a href="' . esc_url( $link ) . '">
                            <div class="state-icon" style="--mask-url:url(\'' . esc_url($svg_file) . '\')"></div>
                            <span>' . esc_html($name) . '</span>
                        </a>';
                } elseif ( $global_assessment_id ) {
                    echo '<pr360-questionnaire url="wss://app.patientreach360.com/socket" site-id="' . esc_attr( $global_assessment_id ) . '">
                            <div class="state-icon" style="--mask-url:url(\'' . esc_url($svg_file) . '\')"></div>
                            <span>' . esc_html($name) . '</span>
                        </pr360-questionnaire>';
                } else {
                    echo '<a href="' . esc_url( $default_clinic_url ) . '">
                            <div class="state-icon" style="--mask-url:url(\'' . esc_url($svg_file) . '\')"></div>
                            <span>' . esc_html($name) . '</span>
                        </a>';
                }
                echo '</li>';
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
