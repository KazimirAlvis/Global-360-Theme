<?php
/**
 * Template Name: Linktree Landing v2
 * Template Post Type: page
 *
 * @package Global-360-Theme
 */

get_header();

while ( have_posts() ) :
	the_post();
	$page_id          = get_the_ID();
	$slides           = get_post_meta( $page_id, GLOBAL360_LINKTREE_V2_SLIDES_META, true );
	$slides           = is_array( $slides ) ? array_values( $slides ) : array();
	$slides           = array_values(
		array_filter(
			$slides,
			static function ( $slide ) {
				return is_array( $slide ) && ! empty( $slide['attachment_id'] ) && wp_attachment_is_image( absint( $slide['attachment_id'] ) );
			}
		)
	);
	$assessment_label = trim( (string) get_post_meta( $page_id, GLOBAL360_LINKTREE_V2_ASSESSMENT_LABEL_META, true ) );
	$website_label    = trim( (string) get_post_meta( $page_id, GLOBAL360_LINKTREE_V2_WEBSITE_LABEL_META, true ) );
	$guide_label      = trim( (string) get_post_meta( $page_id, GLOBAL360_LINKTREE_V2_GUIDE_LABEL_META, true ) );
	$guide_id         = absint( get_post_meta( $page_id, GLOBAL360_LINKTREE_V2_GUIDE_ID_META, true ) );
	$guide_url        = $guide_id ? wp_get_attachment_url( $guide_id ) : '';
	$settings         = global360_theme_site_context();
	$primary_color    = isset( $settings['primary_color'] ) ? sanitize_hex_color( $settings['primary_color'] ) : '';
	$secondary_color  = isset( $settings['secondary_color'] ) ? sanitize_hex_color( $settings['secondary_color'] ) : '';
	$site_name        = isset( $settings['site_name'] ) ? trim( sanitize_text_field( $settings['site_name'] ) ) : '';
	$site_name        = $site_name ? $site_name : get_bloginfo( 'name' );
	$linktree_logo_id = isset( $settings['linktree_logo_id'] ) ? absint( $settings['linktree_logo_id'] ) : 0;
	$linktree_logo_id = $linktree_logo_id ? $linktree_logo_id : ( isset( $settings['header_logo_id'] ) ? absint( $settings['header_logo_id'] ) : 0 );
	$linktree_logo_alt = $linktree_logo_id ? trim( (string) get_post_meta( $linktree_logo_id, '_wp_attachment_image_alt', true ) ) : '';
	$linktree_logo_alt = $linktree_logo_alt ? $linktree_logo_alt : $site_name;
	$linktree_logo    = $linktree_logo_id ? wp_get_attachment_image(
		$linktree_logo_id,
		'medium',
		false,
		array(
			'class'    => 'linktree-v2-identity__logo-image',
			'alt'      => $linktree_logo_alt,
			'loading'  => 'eager',
			'decoding' => 'async',
		)
	) : '';
	$website_url      = '';
	$instagram_url    = '';
	$instagram_label  = '';
	$social_links     = array();
	$allowed_socials  = array( 'facebook', 'linkedin', 'instagram', 'x', 'threads', 'tiktok', 'youtube' );

	foreach ( isset( $settings['social_links'] ) && is_array( $settings['social_links'] ) ? $settings['social_links'] : array() as $row ) {
		$platform = isset( $row['platform'] ) ? sanitize_key( $row['platform'] ) : '';
		$url      = isset( $row['url'] ) ? esc_url_raw( $row['url'] ) : '';
		if ( 'website' === $platform && $url ) {
			$website_url = $url;
		} elseif ( in_array( $platform, $allowed_socials, true ) && $url ) {
			$social_links[] = array( 'platform' => $platform, 'url' => $url );
			if ( 'instagram' === $platform && ! $instagram_url ) {
				$instagram_url = $url;
				$path          = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
				$path_parts    = $path ? explode( '/', $path ) : array();
				$handle        = $path_parts ? rawurldecode( (string) reset( $path_parts ) ) : '';
				$instagram_label = $handle ? '@' . ltrim( $handle, '@' ) : __( 'Instagram', 'cpt360' );
			}
		}
	}
	$website_url      = $website_url ? $website_url : home_url( '/' );
	$assessment_label = $assessment_label ? $assessment_label : __( 'Take Assessment', 'cpt360' );
	$website_label    = $website_label ? $website_label : __( 'Visit Our Website', 'cpt360' );
	$guide_label      = $guide_label ? $guide_label : __( 'Download Guide', 'cpt360' );
	$style_vars       = array();
	if ( $primary_color ) {
		$style_vars[] = '--linktree-v2-primary:' . $primary_color;
	}
	if ( $secondary_color ) {
		$style_vars[] = '--linktree-v2-secondary:' . $secondary_color;
	}
	?>
	<main id="primary" class="site-main linktree-v2-main">
		<section class="linktree-v2"<?php echo $style_vars ? ' style="' . esc_attr( implode( ';', $style_vars ) ) . '"' : ''; ?>>
			<h1 class="screen-reader-text"><?php echo esc_html( get_the_title() ); ?></h1>
			<div class="linktree-v2__inner">
				<?php if ( $slides ) : ?>
					<div class="linktree-v2-carousel" data-linktree-v2-carousel tabindex="0" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured guide', 'cpt360' ); ?>">
						<div class="linktree-v2-carousel__slides" aria-live="polite">
							<?php foreach ( $slides as $index => $slide ) : ?>
								<?php
								$attachment_id = isset( $slide['attachment_id'] ) ? absint( $slide['attachment_id'] ) : 0;
								if ( ! $attachment_id ) {
									continue;
								}
								$alt = isset( $slide['alt'] ) ? trim( (string) $slide['alt'] ) : '';
								if ( '' === $alt ) {
									$alt = (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
								}
								$image = wp_get_attachment_image(
									$attachment_id,
									'full',
									false,
									array(
										'alt'      => $alt,
										'class'    => 'linktree-v2-carousel__image',
										'loading'  => 0 === $index ? 'eager' : 'lazy',
										'decoding' => 'async',
										'sizes'    => '(max-width: 560px) calc(100vw - 40px), 520px',
									)
								);
								if ( ! $image ) {
									continue;
								}
								?>
								<div class="linktree-v2-carousel__slide" data-linktree-v2-slide role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( __( '%1$d of %2$d', 'cpt360' ), $index + 1, count( $slides ) ) ); ?>"<?php echo 0 === $index ? '' : ' hidden'; ?>>
									<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endforeach; ?>
						</div>
						<?php if ( count( $slides ) > 1 ) : ?>
							<div class="linktree-v2-carousel__controls" aria-label="<?php esc_attr_e( 'Carousel pagination controls', 'cpt360' ); ?>">
								<button class="linktree-v2-carousel__arrow linktree-v2-carousel__arrow--previous" type="button" data-linktree-v2-previous aria-label="<?php esc_attr_e( 'Previous slide', 'cpt360' ); ?>"><span aria-hidden="true">&#8249;</span></button>
								<button class="linktree-v2-carousel__arrow linktree-v2-carousel__arrow--next" type="button" data-linktree-v2-next aria-label="<?php esc_attr_e( 'Next slide', 'cpt360' ); ?>"><span aria-hidden="true">&#8250;</span></button>
								<div class="linktree-v2-carousel__dots">
									<?php foreach ( $slides as $index => $slide ) : ?>
										<button type="button" data-linktree-v2-dot="<?php echo esc_attr( $index ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Show slide %d', 'cpt360' ), $index + 1 ) ); ?>" aria-current="<?php echo 0 === $index ? 'true' : 'false'; ?>"><span></span></button>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
						<p class="screen-reader-text" data-linktree-v2-status aria-live="polite"></p>
					</div>
				<?php endif; ?>

				<div class="linktree-v2-actions">
					<div class="linktree-v2-identity">
						<?php if ( $linktree_logo ) : ?>
							<div class="linktree-v2-identity__logo"><?php echo $linktree_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php endif; ?>
						<div class="linktree-v2-identity__text">
							<?php if ( $site_name ) : ?>
								<p class="linktree-v2-identity__name"><?php echo esc_html( $site_name ); ?></p>
							<?php endif; ?>
							<?php if ( $instagram_url ) : ?>
								<a class="linktree-v2-identity__instagram" href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $instagram_label ); ?></a>
							<?php endif; ?>
						</div>
					</div>
					<ul class="linktree-v2-actions__buttons">
						<?php $assessment_site_id = function_exists( 'cpt360_get_assessment_id' ) ? cpt360_get_assessment_id() : ''; ?>
						<?php if ( $assessment_site_id ) : ?>
							<li>
								<?php
								$assessment_inner_html = esc_html( $assessment_label );
								require get_template_directory() . '/clinic-partials/assessment-questionnaire.php';
								?>
							</li>
						<?php endif; ?>
						<li><a class="linktree-v2-button" href="<?php echo esc_url( $website_url ); ?>"><?php echo esc_html( $website_label ); ?></a></li>
						<?php if ( $guide_url ) : ?>
							<li><a class="linktree-v2-button" href="<?php echo esc_url( $guide_url ); ?>" download><?php echo esc_html( $guide_label ); ?></a></li>
						<?php endif; ?>
					</ul>
					<?php if ( $social_links ) : ?>
						<ul class="linktree-v2-socials" aria-label="<?php esc_attr_e( 'Social links', 'cpt360' ); ?>">
							<?php foreach ( $social_links as $social ) : ?>
								<li><a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener"><span class="screen-reader-text"><?php echo esc_html( ucwords( $social['platform'] ) ); ?></span><?php echo global_360_get_icon_svg( $social['platform'], 'linktree-v2-socials__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</section>
	</main>
	<?php
endwhile;

get_footer();
