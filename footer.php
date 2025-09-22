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
				<li><a href="https://myfibroidclinic.com/"><span><img src="<?php echo get_template_directory_uri(); ?>/assets/icons/Fibroid_Clinic_Logomark_Version_2.svg" alt="Fibroid Clinic" /></span>myfibroidclinic.com</a></li>
				<li><a href="https://neuropathy360.com/"><span><img src="<?php echo get_template_directory_uri(); ?>/assets/icons/neuropathy_360_logomark_v2.svg" alt="Neuropathy 360" /></span>neuropathy360.com</a></li>
				<li><a href="https://kneepain360.com/"><span><img src="<?php echo get_template_directory_uri(); ?>/assets/icons/knee_pain_360_logomark_v2.svg" alt="Knee Pain 360" /></span>kneepain360.com</a></li>
				<li><a href="https://myhemorrhoidclinic.com/"><span><img src="<?php echo get_template_directory_uri(); ?>/assets/icons/hemorrhoid_clinic_logomark_v2.svg" alt="Hemorrhoid Clinic" /></span>myhemorrhoidclinic.com</a></li>
				<li><a href="https://myprostateclinic.com/"><span><img src="<?php echo get_template_directory_uri(); ?>/assets/icons/prostate_clinic_logomark_V2.svg" alt="Prostate Clinic" /></span>myprostateclinic.com</a></li>
			</ul>

		</div><!-- .offsite_links -->
		<div class="column contact_links">
			<h4>Contact</h4>
			<ul>
				<li><span>Email:</span><a href="mailto:info@myhemorrhoidclinic.com">Customer Support</a></li>
				<li><span>Phone:</span><a href="tel:+15135876827">513-587-6827</a></li>

			</ul>


		</div><!-- .sm_links -->

	</div><!-- .footer_inner -->
	<div class="border max_width_content"></div>
	<div class="lower_footer max_width_content">
		<div class="legal">
			<?php
			$opts = get_option('360_global_settings', []);
			$site_name = isset($opts['site_name']) && !empty($opts['site_name']) ? $opts['site_name'] : get_bloginfo('name');
			$current_year = date('Y');
			?>
			<p>Copyright © <?php echo $current_year; ?> <?php echo esc_html($site_name); ?>. All Rights Reserved</p>
			<p><a href="https://www.patientreach360.com/privacy-policy/">Privacy Policy</a> | <a href="https://www.patientreach360.com/terms-of-use/">Terms of Use Agreement</a> | <a href="">Sitemap</a></p>
		</div>
		<div class="footer_form_pu">
			<span id="do-not-sell-trigger" style="cursor: pointer;">Do Not Sell MY Info</span>
		</div>
	</div>

	<!-- Do Not Sell Info Modal -->
	<div id="do-not-sell-modal" class="modal-overlay" style="display: none;">
		<div class="modal-content">
			<div class="modal-header">
				<h3>Do Not Sell My Information</h3>
				<span class="modal-close">&times;</span>
			</div>
			<div class="modal-body">
				<?php echo do_shortcode('[contact-form-7 id="98f6667" title="Do Not Sell info Form"]'); ?>
			</div>
		</div>
	</div>

</footer><!-- #colophon -->
</div><!-- #page -->

<script>
document.addEventListener('DOMContentLoaded', function() {
	const trigger = document.getElementById('do-not-sell-trigger');
	const modal = document.getElementById('do-not-sell-modal');
	const closeBtn = modal.querySelector('.modal-close');

	// Open modal
	trigger.addEventListener('click', function() {
		modal.style.display = 'flex';
		setTimeout(() => {
			modal.classList.add('modal-open');
		}, 10);
		document.body.style.overflow = 'hidden'; // Prevent background scrolling
	});

	// Close modal
	function closeModal() {
		modal.classList.remove('modal-open');
		setTimeout(() => {
			modal.style.display = 'none';
		}, 300);
		document.body.style.overflow = ''; // Restore scrolling
	}

	// Close on X button click
	closeBtn.addEventListener('click', closeModal);

	// Close on overlay click
	modal.addEventListener('click', function(e) {
		if (e.target === modal) {
			closeModal();
		}
	});

	// Close on Escape key
	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape' && modal.classList.contains('modal-open')) {
			closeModal();
		}
	});
});
</script>

<?php wp_footer(); ?>

</body>

</html>