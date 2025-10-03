<?php
  // debug
  $v2 = __DIR__ . '/clinic-button-v2.php';
  echo "<!-- looking for V2 button in: {$v2} (exists? " . ( file_exists($v2) ? 'yes' : 'no' ) . ") -->";
?>
<?php 
$bg = get_template_directory_uri() . '/images/cta-background.jpg';
?>

<div 
  class="clinic-cta-BG-wrap" 
  style="
    background-image: url('<?php echo esc_url( $bg ); ?>');
    background-size: cover;
    background-position: center;
    padding: 4rem 1rem;
    color: #fff;
  "
>
  <div class="clinic-cta-inner">
    <h3>Find out if you are a candidate</h3>
        <?php
    $btn2 = __DIR__ . '/clinic-button-v2.php';
    if (file_exists($btn2)) {
        require $btn2;
    } else {
        echo '<!-- clinic-button-v2.php not found -->';
    }
    ?>
  </div>
</div>
