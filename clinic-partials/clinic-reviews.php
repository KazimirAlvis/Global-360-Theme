<?php
$reviews = get_post_meta( get_the_ID(), 'clinic_reviews', true );
if ( ! empty( $reviews ) && is_array( $reviews ) ): ?>
  <section class="clinic-reviews">
    <h2>Patient Reviews</h2>
    <div class="clinic-reviews-slider">
      <?php foreach ( $reviews as $r ): ?>
        <div class="review-slide">
          <i class="fa-solid fa-quote-right review-quote-icon"></i>
          <blockquote class="review-text">
            <?php echo nl2br( esc_html( $r['review'] ) ); ?>
          </blockquote>
          <cite class="reviewer-name">— <?php echo esc_html( $r['reviewer'] ); ?></cite>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>