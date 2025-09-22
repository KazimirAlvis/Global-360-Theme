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
  .main-navigation ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
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
  }
  @media screen and (max-width: 37.5em) {
    .menu-toggle {
      display: block;
    }
    .main-navigation ul {
      display: none;
    }
    .main-navigation.toggled ul {
      display: block;
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
				$logo_id = isset($opts['header_logo_id']) ? $opts['header_logo_id'] : '';
				if ($logo_id) {
					$logo_url = wp_get_attachment_image_url($logo_id, 'medium');
					if ($logo_url) {
						echo '<a href="/"><img src="' . esc_url($logo_url) . '" alt="Header Logo" class="site-header-logo" /></a>';
					}
				}
				?>
			</div><!-- .site-branding -->
			<nav id="site-navigation" class="main-navigation">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e('Primary Menu', 'global-360-theme'); ?></button>
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