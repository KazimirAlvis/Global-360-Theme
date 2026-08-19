<?php
$clinic_view = global360_theme_clinic( get_the_ID() );
$reviews = $clinic_view['reviews'] ?? array();
if ( ! empty( $reviews ) && is_array( $reviews ) ): ?>
  <section class="clinic-reviews">
    <h2>Patient Reviews</h2>
    <div class="clinic-reviews-slider">
      <?php foreach ( $reviews as $r ): ?>
        <div class="review-slide">
          <?php echo global_360_get_icon_svg('quote', 'review-quote-icon'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          <blockquote class="review-text">
            <?php echo nl2br( esc_html( $r['review'] ) ); ?>
          </blockquote>
          <cite class="reviewer-name">— <?php echo esc_html( $r['reviewer'] ); ?></cite>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>
