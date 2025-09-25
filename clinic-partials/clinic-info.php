<h2>For <?php the_title(); ?> Patients</h2>
<?php
    // Clinic info repeater 
    $clinic_info = get_post_meta(get_the_ID(), 'clinic_info', true);
    

    // Make sure it’s an array and not empty
    if (! empty($clinic_info) && is_array($clinic_info)) : ?>
      <section class="clinic_info">
          <?php foreach ($clinic_info as $info) :
                // Clean up our values
                $title = sanitize_text_field($info['title'] ?? '');
                $desc  = wp_kses_post($info['description'] ?? '');

                // Only output if we actually have something
                if ($title || $desc) : ?>
                  <div class="clinic-info_item">
                      <?php if ($title) : ?>
                          <h3 class="clinic_info_title"><?php echo esc_html($title); ?></h3>
                      <?php endif; ?>

                      <?php if ($desc) : ?>
                          <div class="clinic-info_description">
                              <?php
                                // Wrap paragraphs automatically
                                echo wpautop($desc);
                                ?>
                          </div>
                      <?php endif; ?>
                  </div>
          <?php endif;
            endforeach; ?>
      </section>
  <?php endif; ?>