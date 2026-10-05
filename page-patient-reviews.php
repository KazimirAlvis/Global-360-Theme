<?php
/** Public Patient Reviews page; all data selection belongs to Platform Core. */
get_header();
$page_number = isset( $_GET['review_page'] ) && is_scalar( $_GET['review_page'] ) ? max( 1, absint( $_GET['review_page'] ) ) : 1;
$reviews = function_exists( 'global360_platform' ) && method_exists( global360_platform(), 'patient_reviews' )
	? global360_platform()->patient_reviews()->query( array( 'page' => $page_number, 'per_page' => 9 ) )
	: array( 'items' => array(), 'total' => 0, 'pages' => 0, 'page' => 1 );
?>
<main id="primary" class="site-main patient-reviews-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'template-parts/content', 'page' ); ?>
	<?php endwhile; ?>
	<section class="patient-reviews-listing" aria-label="<?php esc_attr_e( 'Patient reviews', 'global-360-theme' ); ?>">
	<?php if ( ! $reviews['items'] ) : ?>
		<p class="patient-reviews-empty"><?php esc_html_e( 'No patient reviews have been published yet.', 'global-360-theme' ); ?></p>
	<?php else : ?>
		<div class="patient-reviews-grid">
		<?php foreach ( $reviews['items'] as $review ) : ?>
			<article class="patient-review-card">
				<h2><?php if ( $review['clinic_url'] ) : ?><a href="<?php echo esc_url( $review['clinic_url'] ); ?>"><?php echo esc_html( $review['clinic_name'] ); ?></a><?php else : ?><?php echo esc_html( $review['clinic_name'] ); ?><?php endif; ?></h2>
				<?php if ( $review['doctor_id'] ) : ?>
					<p class="patient-review-doctor"><?php if ( $review['doctor_url'] && $review['doctor_name'] ) : ?><a href="<?php echo esc_url( $review['doctor_url'] ); ?>"><?php echo esc_html( $review['doctor_name'] ); ?></a><?php else : ?><?php echo esc_html( $review['doctor_name'] ?: __( 'Doctor no longer listed', 'global-360-theme' ) ); ?><?php endif; ?></p>
				<?php else : ?>
					<p class="patient-review-doctor"><?php esc_html_e( 'Clinic overall', 'global-360-theme' ); ?></p>
				<?php endif; ?>
				<p class="patient-review-rating"><?php echo esc_html( sprintf( __( 'Rated %d out of 5', 'global-360-theme' ), $review['rating'] ) ); ?></p>
				<blockquote><p><?php echo nl2br( esc_html( $review['review_text'] ) ); ?></p></blockquote>
				<footer>
					<strong><?php echo esc_html( $review['display_name'] ); ?></strong>
					<time datetime="<?php echo esc_attr( str_replace( ' ', 'T', $review['submitted_at'] ) . 'Z' ); ?>"><?php echo esc_html( get_date_from_gmt( $review['submitted_at'], get_option( 'date_format' ) ) ); ?></time>
				</footer>
			</article>
		<?php endforeach; ?>
		</div>
		<?php if ( $reviews['pages'] > 1 ) : ?>
			<nav class="patient-reviews-pagination" aria-label="<?php esc_attr_e( 'Patient reviews pages', 'global-360-theme' ); ?>">
			<?php
			$links = paginate_links( array(
				'base' => add_query_arg( 'review_page', '%#%', get_permalink() ), 'format' => '',
				'current' => $reviews['page'], 'total' => $reviews['pages'], 'type' => 'list',
				'prev_text' => __( 'Previous', 'global-360-theme' ), 'next_text' => __( 'Next', 'global-360-theme' ),
				'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page ', 'global-360-theme' ) . '</span>',
			) );
			echo wp_kses_post( $links ?? '' );
			?>
			</nav>
		<?php endif; ?>
	<?php endif; ?>
	</section>
</main>
<?php get_footer(); ?>
