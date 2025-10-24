<?php
// Pull the ID via our helper (per-clinic or fallback to global)
$assess_id = cpt360_get_assessment_id();

if ( $assess_id ) :
?>

  <!-- PR360 Risk Assessment Component -->
  <pr360-questionnaire
      url="wss://app.patientreach360.com/socket"
      site-id="<?php echo esc_attr( $assess_id ); ?>">
    Take Risk Assessment Now
  </pr360-questionnaire>

<?php endif; ?>
