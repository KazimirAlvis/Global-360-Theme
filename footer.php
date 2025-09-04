<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Global-360-Theme
 */

?>

<footer id="colophon" class="site-footer">
	<div class="footer_inner max_width_content">
		<div class="column footer_logo">
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
				<h4>Follow Us</h4>
			<ul class="sm_links_list">
			<?php
			$opts = get_option('360_global_settings', []);
			if (!empty($opts['social_links']) && is_array($opts['social_links'])) {
				// Font Awesome icon map
				$fa_map = [
					'facebook' => 'fab fa-facebook-f',
					'instagram' => 'fab fa-instagram',
					'x' => 'fab fa-x-twitter',
					'youtube' => 'fab fa-youtube',
					'tiktok' => 'fab fa-tiktok',
					'linkedin' => 'fab fa-linkedin-in',
					'website' => 'fas fa-globe',
				];
				foreach ($opts['social_links'] as $row) {
					$platform = isset($row['platform']) ? $row['platform'] : '';
					$url = isset($row['url']) ? $row['url'] : '';
					if ($platform && $url) {
						$icon = isset($fa_map[$platform]) ? $fa_map[$platform] : 'fas fa-link';
						echo '<li><a href="' . esc_url($url) . '" target="_blank" rel="noopener"><i class="' . esc_attr($icon) . '"></i></a></li>';
					}
				}
			}
			?>
			</ul>
		</div><!-- .footer_logo -->
		<div class="column site_links">
			<h4>Site Links</h4>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
				)
			);
			?>
		</div><!-- .site_links -->
		<div class="column offsite_links">
			<h4>The 360 Network</h4>
			<ul>
				<li><a href="https://myfibroidclinic.com/"><span></span>myfibroidclinic.com</a></li>
				<li><a href="https://neuropathy360.com/"><span></span>neuropathy360.com</a></li>
				<li><a href="https://kneepain360.com/"><span></span>kneepain360.com</a></li>
				<li><a href="https://myhemorrhoidclinic.com/"><span></span>myhemorrhoidclinic.com</a></li>
				<li><a href="https://myprostateclinic.com/"><span></span>myprostateclinic.com</a></li>
			</ul>

		</div><!-- .offsite_links -->
		<div class="column contact_links">
			<h4>Contact</h4>
						<ul>
				<li><span>Email</span><a href="info@myhemorrhoidclinic.com">Customer Support</a></li>
				<li><span>Phone</span><a href="tel:+15135876827">513-587-6827</a></li>

			</ul>

		
		</div><!-- .sm_links -->
		
	</div><!-- .footer_inner -->
	<div class="border max_width_content"></div>
	<div class="legal">
		<p>Copyright © 2025 Hemorrhoid Clinic. All Right Reserved</p>
	</div>

</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>