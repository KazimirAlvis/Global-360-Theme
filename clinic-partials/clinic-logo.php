<?php
$logo_url = cpt360_get_clinic_logo_url();
if ( $logo_url ): ?>
  <div class="clinic-logo">
    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php the_title_attribute(); ?>">
  </div>
<?php endif; ?>
