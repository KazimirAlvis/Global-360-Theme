<?php 
$bg = get_template_directory_uri() . '/images/clinic-hero.jpg';
?>

<div 
  class="clinic-hero-BG-wrap" 
  style="
    background-image: url('<?php echo esc_url( $bg ); ?>');
    background-size: cover;
    background-position: center;
    padding: 4rem 1rem;
    color: #fff;
    min-height: 40vh;
  "
>
  <div class="clinic-hero-inner">
   <?php the_title('<h1 class="clinic-title">', '</h1>'); ?>

  </div>
  <div class="blk_overlay"></div>
</div>
