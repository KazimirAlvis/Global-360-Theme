<?php
// 1) Use our helper which falls back to the global setting
$assess_id = cpt360_get_assessment_id();

if ( $assess_id ) :

  // 2) (Optional) Build a URL if you need it elsewhere
  $url = esc_url( home_url( '/take-assessment/?clinic_id=' . $assess_id ) );

  // 3) Determine if we should add "btn btn_green" locally
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
  <?php
$assess_id = cpt360_get_assessment_id();

if ( $assess_id ) :
  // TEMPORARY: Clear the per-clinic override to use global setting
  $post_id = get_the_ID();
  if ($post_id == 16814) {
    delete_post_meta($post_id, '_cpt360_assessment_id');
    echo "<!-- CLEARED clinic meta for post 16814 -->\n";
    // Refresh the ID after clearing
    $assess_id = cpt360_get_assessment_id();
  }
  
  // Debug info
  $clinic_meta = get_post_meta($post_id, '_cpt360_assessment_id', true);
  $global_settings = get_option('360_global_settings', []);
  $global_assessment_id = $global_settings['assessment_id'] ?? '';
  
  echo "<!-- PR360 ID Debug AFTER CLEARING:\n";
  echo "Post ID: {$post_id}\n";
  echo "Clinic Meta (_cpt360_assessment_id): " . ($clinic_meta ?: 'EMPTY') . "\n";
  echo "Global Settings (assessment_id): " . ($global_assessment_id ?: 'EMPTY') . "\n";
  echo "Final assess_id: {$assess_id}\n";
  echo "-->\n";
?>

  <!-- Minimal PR360 implementation -->
  <pr360-questionnaire 
    url="wss://app.patientreach360.com/socket" 
    site-id="<?php echo esc_attr( $assess_id ); ?>">
    Take Risk Assessment Now
  </pr360-questionnaire>

<?php endif; ?>

<?php
else: ?>
  <!-- DEBUG: No assessment ID found -->
  <div style="padding: 10px; background: #f0f0f0; border: 1px solid #ccc; margin: 10px 0;">
    <strong>Debug:</strong> No assessment ID configured. Please check:
    <ul>
      <li>Clinic-specific assessment ID meta field</li>
      <li>Global assessment ID in 360 Settings</li>
    </ul>
  </div>
<?php endif; ?>