<?php
  the_clinic_bio([
    'before'        => '<section class="clinic-bio"><h2>About ' . esc_html( get_the_title() ) . '</h2>',
    'after'         => '</section>',
    'apply_filters' => true,
  ]);
?>
