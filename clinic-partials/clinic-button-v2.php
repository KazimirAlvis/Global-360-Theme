<script>
  // 1) Your existing “?open” logic
  const urlParams = new URLSearchParams(window.location.search);
  const shouldOpen = urlParams.has('open');
  window.addEventListener('DOMContentLoaded', () => {
    const questionnaire = document.querySelector('pr360-questionnaire');
    if ( shouldOpen && questionnaire ) {
      questionnaire.setAttribute('open', '');
    }
  });
</script>

<?php
// 2) Pull the ID via our new helper (per‐clinic or fallback to global)
$assess_id = cpt360_get_assessment_id();

if ( $assess_id ) :

  // 3) (Optional) Build a clickable link if you need one
  $take_url = esc_url( home_url( '/take-assessment/?clinic_id=' . $assess_id ) );

  // 4) Only add .btn .btn_green on local/dev
  $env = function_exists( 'wp_get_environment_type' )
       ? wp_get_environment_type()
       : 'production';

  $classes = ( 'development' === $env ) 
           ? [ 'btn', 'btn_green' ] 
           : [];

  $class_attr = $classes
              ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"'
              : '';
?>

  <!-- Your PR360 component — just echo the dynamic attributes -->
  <pr360-questionnaire
      url="wss://app.patientreach360.com/socket"<?php echo $class_attr; ?>
      site-id="<?php echo esc_attr( $assess_id ); ?>">
    Take Risk Assessment Now
  </pr360-questionnaire>

 

<?php
endif;
?>
