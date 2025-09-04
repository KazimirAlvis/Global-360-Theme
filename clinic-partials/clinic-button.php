<script>
  // your “open on ?open” logic stays unchanged
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
// 1) Use our helper which falls back to the global setting
$assess_id = cpt360_get_assessment_id();

if ( $assess_id ) :

  // 2) (Optional) Build a URL if you need it elsewhere
  $url = esc_url( home_url( '/take-assessment/?clinic_id=' . $assess_id ) );

  // 3) Determine if we should add “btn btn_green” locally
  $env = function_exists('wp_get_environment_type')
         ? wp_get_environment_type()
         : 'production';

  $classes = [];
  if ( 'development' === $env ) {
      $classes = [ 'btn', 'btn_green' ];
  }

  $class_attr = $classes
    ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"'
    : '';
  ?>

  <!-- Your PR360 component -->
  <pr360-questionnaire
      url="wss://app.patientreach360.com/socket"<?php echo $class_attr; ?>
      site-id="<?php echo esc_attr( $assess_id ); ?>">
      Take Risk Assessment Now
  </pr360-questionnaire>

<?php
endif;
