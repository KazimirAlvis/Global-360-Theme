<?php

/**
 * single-clinic.php
 * Template for displaying single Clinic posts
 */

get_header();


?>

<main id="site-content test" role="main">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

      <article <?php post_class('clinic-single'); ?>>

        <?php
       $theme_root = get_template_directory();
        ?>
        <section class="clinic_hero ">
          <?php  require_once $theme_root . '/clinic-partials/clinic-hero.php'; ?>
        </section>
        <div class="clinic-content">
          <section class="two_column clinic_main_info">
            <div class="column">
              <?php require_once $theme_root . '/clinic-partials/clinic-logo.php'; ?>
              <h4>Clinic Information</h4>
              <?php require_once $theme_root . '/clinic-partials/clinic-address.php'; ?>
              <?php require_once $theme_root . '/clinic-partials/clinic-google-reviews.php'; ?>
              
            </div>
            <div class="column">
              
              <?php require_once $theme_root . '/clinic-partials/clinic-button.php'; ?>
              <?php require_once $theme_root . '/clinic-partials/clinic-phone-button.php'; ?>
            </div>
          </section>

          <section class="clinic_bio text-content">
            <?php require_once $theme_root . '/clinic-partials/clinic-bio.php'; ?>
          </section>
          <section class="clinic_web_btn">
            <?php require_once $theme_root . '/clinic-partials/clinic-website.php'; ?>
          </section>
          <section class="clinic_maps_wrapper">
            <?php require_once $theme_root . '/clinic-partials/clinic-maps.php'; ?>
          </section>
          <section class="clinic_doctors_wrapper">
            <?php
              require_once $theme_root . '/inc/meta-boxes/clinic-doctors.php';
            // just call the function — no need to require the file again
            if (function_exists('cpt360_render_clinic_doctors')) {
              cpt360_render_clinic_doctors();
            }
            ?>
          </section>
          <section class="clinic_info_wrap">
            <?php require_once $theme_root . '/clinic-partials/clinic-info.php'; ?>
          </section>
          <section class="clinic_reviews_wrap">
            <?php require_once $theme_root . '/clinic-partials/clinic-reviews.php'; ?>
          </section>
        </div>
        <section class="clinic_cta_wrap">
          <?php require_once $theme_root . '/clinic-partials/clinic-cta.php'; ?>
        </section>

      </article>

  <?php endwhile;
  endif; ?>
</main>

<?php get_footer(); ?>

