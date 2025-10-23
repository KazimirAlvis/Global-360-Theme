<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Global-360-Theme
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
	
	<!-- PR360 Questionnaire Script -->
	<script type="module" src="https://unpkg.com/pr360-questionnaire"></script>
	
	<!-- Open Graph / Social Media Meta Tags -->
	<?php if (is_single() && get_post_type() == 'post') : ?>
		<meta property="og:type" content="article" />
		<meta property="og:title" content="<?php echo esc_attr(get_the_title()); ?>" />
		<meta property="og:description" content="<?php echo esc_attr(wp_trim_words(get_the_excerpt(), 20, '...')); ?>" />
		<meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>" />
		<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>" />
		<?php if (has_post_thumbnail()) : ?>
			<meta property="og:image" content="<?php echo esc_url(get_the_post_thumbnail_url(null, 'large')); ?>" />
			<meta property="og:image:width" content="1200" />
			<meta property="og:image:height" content="630" />
		<?php endif; ?>
		
		<!-- Twitter Card -->
		<meta name="twitter:card" content="summary_large_image" />
		<meta name="twitter:title" content="<?php echo esc_attr(get_the_title()); ?>" />
		<meta name="twitter:description" content="<?php echo esc_attr(wp_trim_words(get_the_excerpt(), 20, '...')); ?>" />
		<?php if (has_post_thumbnail()) : ?>
			<meta name="twitter:image" content="<?php echo esc_url(get_the_post_thumbnail_url(null, 'large')); ?>" />
		<?php endif; ?>
		
		<!-- LinkedIn specific -->
		<meta property="linkedin:owner" content="<?php echo esc_attr(get_bloginfo('name')); ?>" />
	<?php else : ?>
		<meta property="og:type" content="website" />
		<meta property="og:title" content="<?php echo esc_attr(wp_get_document_title()); ?>" />
		<meta property="og:description" content="<?php echo esc_attr(get_bloginfo('description')); ?>" />
		<meta property="og:url" content="<?php echo esc_url(home_url()); ?>" />
		<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>" />
	<?php endif; ?>
	
	
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'global-360-theme'); ?></a>

		<header id="masthead" class="site-header">
			<div class="header_inner max_width_content">
			<div class="site-branding">
				<?php
				$opts = get_option('360_global_settings', []);
				$logo_id = isset($opts['header_logo_id']) ? (int) $opts['header_logo_id'] : 0;
				if ($logo_id) {
					$logo_alt = get_post_meta($logo_id, '_wp_attachment_image_alt', true);
					if (!$logo_alt) {
						$logo_alt = get_bloginfo('name');
					}
					$logo_alt = sanitize_text_field($logo_alt);
					$logo_html = wp_get_attachment_image(
						$logo_id,
						'full',
						false,
						[
							'class'   => 'site-header-logo',
							'alt'     => $logo_alt,
							'loading' => 'eager',
						]
					);
					if ($logo_html) {
						echo '<a href="/" class="site-header-logo-link">' . $logo_html . '</a>';
					}
				}
				?>
			</div><!-- .site-branding -->
			<nav id="site-navigation" class="main-navigation" aria-label="Primary">
				<button class="menu-toggle" type="button" aria-controls="primary-menu" aria-expanded="false" aria-label="Toggle primary navigation">
					<span class="menu-toggle-box" aria-hidden="true">
						<span class="menu-toggle-bar"></span>
						<span class="menu-toggle-bar"></span>
						<span class="menu-toggle-bar"></span>
					</span>
					<span class="screen-reader-text">Menu</span>
				</button>
				<button class="menu-close" type="button" aria-controls="primary-menu" aria-expanded="false" aria-hidden="true">
					<span aria-hidden="true">&times;</span>
					<span class="screen-reader-text">Close menu</span>
				</button>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-1',
						'menu_id'        => 'primary-menu',
					)
				);
				?>
			</nav><!-- #site-navigation -->
			</div>
		</header><!-- #masthead -->