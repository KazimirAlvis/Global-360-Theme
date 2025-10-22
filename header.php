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
	
	<?php
$opts = get_option('360_global_settings', []);
$body_font = isset($opts['body_font']) ? $opts['body_font'] : 'system-font';
$heading_font = isset($opts['heading_font']) ? $opts['heading_font'] : 'system-font';

// Map slug to actual font-family string
$opts = get_option('360_global_settings', []);
$body_font = isset($opts['body_font']) ? $opts['body_font'] : 'system-font';
$heading_font = isset($opts['heading_font']) ? $opts['heading_font'] : 'system-font';

$font_map = [
    'system-font'  => 'system-ui, Arial, sans-serif',
	'anton'        => "'Anton', sans-serif",
    'arvo'         => "'Arvo', serif",
    'bodoni-moda'  => "'Bodoni Moda', serif",
    'cabin'        => "'Cabin', sans-serif",
    'chivo'        => "'Chivo', sans-serif",
    'roboto'       => "'Roboto', sans-serif",
    'marcellus'    => "'Marcellus', serif",
    'inter'        => "'Inter', sans-serif",
];

echo '<style>
	body { font-family: ' . (isset($font_map[$body_font]) ? $font_map[$body_font] : $font_map['system-font']) . '; }
	h1, h2, h3, h4, h5, h6 { font-family: ' . (isset($font_map[$heading_font]) ? $font_map[$heading_font] : $font_map['system-font']) . '; }
  
	/* Critical CSS to prevent FOUC in navigation */
	.main-navigation {
		position: relative;
	}
	.main-navigation ul {
		list-style: none;
		margin: 0;
		padding: 0;
		display: flex;
		gap: 20px;
	}
	.main-navigation li {
		position: relative;
	}
	.main-navigation a {
		display: block;
		text-decoration: none;
		color: inherit;
		padding: 10px 15px;
	}
	.menu-toggle {
		display: none;
		background: transparent;
		border: 0;
		padding: 0;
		cursor: pointer;
		align-items: center;
		justify-content: center;
	}
	.menu-toggle-box {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		gap: 6px;
	}
	.menu-toggle-bar {
		width: 24px;
		height: 2px;
		background: #ffffff;
		transition: transform 0.3s ease, opacity 0.3s ease;
	}
	.menu-toggle.is-active .menu-toggle-bar:nth-child(1) {
		transform: translateY(8px) rotate(45deg);
	}
	.menu-toggle.is-active .menu-toggle-bar:nth-child(2) {
		opacity: 0;
	}
	.menu-toggle.is-active .menu-toggle-bar:nth-child(3) {
		transform: translateY(-8px) rotate(-45deg);
	}
	body.mobile-menu-open {
		overflow: hidden;
	}
	@media screen and (max-width: 64em) {
		.menu-toggle {
			display: flex;
			width: 48px;
			height: 48px;
			border: 1px solid rgba(255, 255, 255, 0.6);
			border-radius: 8px;
			z-index: 1001;
		}
		.main-navigation ul {
			display: none;
			position: fixed;
			inset: 0;
			padding: 110px 24px 32px;
			background-color: #292626;
			flex-direction: column;
			gap: 24px;
			align-items: flex-start;
			overflow-y: auto;
			z-index: 1000;
		}
		.main-navigation ul li {
			width: 100%;
		}
		.main-navigation ul li a {
			width: 100%;
			font-size: 1.125rem;
			padding: 12px 0;
		}
		.main-navigation.toggled ul {
			display: flex;
		}
	}
</style>';
?>
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
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Toggle primary navigation">
					<span class="menu-toggle-box" aria-hidden="true">
						<span class="menu-toggle-bar"></span>
						<span class="menu-toggle-bar"></span>
						<span class="menu-toggle-bar"></span>
					</span>
					<span class="screen-reader-text">Menu</span>
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