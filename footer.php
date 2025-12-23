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
						'class'   => 'footer-logo-image',
						'alt'     => $logo_alt,
						'loading' => 'lazy',
					]
				);
				if ($logo_html) {
					echo '<a href="/" class="site-footer-logo-link">' . $logo_html . '</a>';
				}
			}
			?>
			<h4>Follow Us</h4>
			<ul class="sm_links_list">
				<?php
				$label_map = [
					'facebook' => 'Facebook',
					'instagram' => 'Instagram',
					'x' => 'X',
					'youtube' => 'YouTube',
					'tiktok' => 'TikTok',
					'linkedin' => 'LinkedIn',
				];
				if (!empty($opts['social_links']) && is_array($opts['social_links'])) {
					foreach ($opts['social_links'] as $row) {
						$platform = isset($row['platform']) ? sanitize_key($row['platform']) : '';
						$url = isset($row['url']) ? $row['url'] : '';
						if ($platform && $url) {
							$label = isset($label_map[$platform]) ? $label_map[$platform] : ucwords(str_replace('-', ' ', $platform));
							$icon = global_360_get_icon_svg($platform, 'social-icon__svg');
							if ($icon) {
								echo '<li><a href="' . esc_url($url) . '" target="_blank" rel="noopener" class="social-icon">' . $icon . '<span class="screen-reader-text">' . esc_html($label) . '</span></a></li>';
							}
						}
					}
				}
				?>
			</ul>
			<?php
			// Get become provider URL from settings and display button here
			$opts = get_option('360_global_settings', []);
			$become_provider_url = isset($opts['become_provider_url']) && !empty($opts['become_provider_url']) ? $opts['become_provider_url'] : '';
			if ($become_provider_url): ?>
				<div class="become-provider-button" style="margin-top: 25px;">
					<a href="<?php echo esc_url($become_provider_url); ?>" class="btn btn-secondary" target="_blank" rel="noopener">Become a Provider</a>
				</div>
			<?php endif; ?>
		</div><!-- .footer_logo -->
		<div class="column site_links">
			<h4>Site Links</h4>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'footer-menu',
					'menu_class'     => 'footer-nav',
					'container_class' => 'footer-nav-container',
					'depth'          => 1,
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
				<?php
				// Get dynamic contact information from settings
				$opts = get_option('360_global_settings', []);
				$contact_email = isset($opts['contact_email']) && !empty($opts['contact_email']) ? $opts['contact_email'] : 'info@myhemorrhoidclinic.com';
				$contact_phone = isset($opts['contact_phone']) && !empty($opts['contact_phone']) ? $opts['contact_phone'] : '513-587-6827';
				$email_label = isset($opts['contact_email_label']) && !empty($opts['contact_email_label']) ? $opts['contact_email_label'] : 'Customer Support';
				?>
				<li><span>Email:</span><a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($email_label); ?></a></li>
				<li><span>Phone:</span><a href="tel:<?php echo esc_attr(preg_replace('/[^\d\+]/', '', $contact_phone)); ?>"><?php echo esc_html($contact_phone); ?></a></li>
			</ul>
		</div><!-- .contact_links -->

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

<?php 
// Floating assessment button - uses same logic as clinic buttons
echo '<!-- DEBUG: Checking for floating button -->';
if (function_exists('cpt360_get_assessment_id')) {
    $assess_id = cpt360_get_assessment_id();
    echo '<!-- DEBUG: Assessment ID found: "' . $assess_id . '" -->';
    if (!empty($assess_id)) {
        ?>
	<div id="floating-assessment-button" class="pr360-pulse-enabled" style="position: fixed; bottom: 20px; right: 20px; z-index: 99999; display: block;">
            <pr360-questionnaire 
                url="wss://app.patientreach360.com/socket" 
                site-id="<?php echo esc_attr($assess_id); ?>">
                Take risk assessment now
            </pr360-questionnaire>
        </div>
        <?php
        echo '<!-- DEBUG: Button rendered -->';
    } else {
        echo '<!-- DEBUG: No assessment ID, not showing button -->';
        
        // Fallback - try global settings directly
        $settings = get_option('360_global_settings', []);
        $global_id = isset($settings['assessment_id']) ? $settings['assessment_id'] : '';
        echo '<!-- DEBUG: Global settings assessment_id: "' . $global_id . '" -->';
        
        if (!empty($global_id)) {
            ?>
			<div id="floating-assessment-button" class="pr360-pulse-enabled">
                <pr360-questionnaire 
                    url="wss://app.patientreach360.com/socket" 
                    site-id="<?php echo esc_attr($global_id); ?>">
                    Take risk assessment now
                </pr360-questionnaire>
            </div>
            <?php
            echo '<!-- DEBUG: Fallback button rendered -->';
        }
    }
} else {
    echo '<!-- DEBUG: cpt360_get_assessment_id function not available -->';
    // Show button with test ID for now
    ?>
	<div id="floating-assessment-button" class="pr360-pulse-enabled">
        <pr360-questionnaire 
            url="wss://app.patientreach360.com/socket" 
            site-id="TEST-ID">
            Take risk assessment now
        </pr360-questionnaire>
    </div>
    <?php
}
?>

	<script>
	(function() {
		function ready(fn) {
			if (document.readyState !== 'loading') {
				fn();
			} else {
				document.addEventListener('DOMContentLoaded', fn, { once: true });
			}
		}

		ready(function() {
			const wrapper = document.getElementById('floating-assessment-button');
			if (!wrapper) {
				return;
			}

			const component = wrapper.querySelector('pr360-questionnaire');
			if (!component) {
				return;
			}

			const applyPulseEffect = () => {
				if (!component.shadowRoot) {
					return false;
				}

				const targetButton = component.shadowRoot.querySelector('button[part="begin-button"], button');
				if (targetButton) {
					if (!targetButton.dataset.pr360PulseApplied) {
						targetButton.classList.add('pr360-pulse-btn');
						targetButton.style.position = targetButton.style.position || 'relative';
						targetButton.style.transformOrigin = 'center';
						targetButton.style.filter = 'drop-shadow(0 6px 12px rgba(0, 0, 0, 0.3))';
						try {
							if (!targetButton._pr360PulseAnimation) {
								targetButton._pr360PulseAnimation = targetButton.animate([
									{ transform: 'scale(1)', boxShadow: '0 0 0 0 rgba(0, 0, 0, 0.45)' },
									{ transform: 'scale(1.07)', boxShadow: '0 0 26px 14px rgba(0, 0, 0, 0.25)', offset: 0.55 },
									{ transform: 'scale(1)', boxShadow: '0 0 0 0 rgba(0, 0, 0, 0)' }
								], {
									duration: 1800,
									easing: 'ease-in-out',
									iterations: Infinity
								});
							}
						} catch (err) {
							// If Web Animations API isn't available, fall back to CSS animation via class.
						}
						targetButton.dataset.pr360PulseApplied = '1';
					}
					return true;
				}

				return false;
			};

			const observeShadow = () => {
				if (!component.shadowRoot) {
					return false;
				}

				const observer = new MutationObserver(() => {
					applyPulseEffect();
				});

				observer.observe(component.shadowRoot, { childList: true, subtree: true });
				applyPulseEffect();
				return true;
			};

			const bootstrapPulse = () => {
				if (observeShadow()) {
					return;
				}

				let attempts = 0;
				const maxAttempts = 40;
				const poll = () => {
					if (observeShadow()) {
						return;
					}
					attempts += 1;
					if (attempts < maxAttempts) {
						setTimeout(poll, 250);
					}
				};

				poll();
			};

			if (window.customElements && customElements.whenDefined) {
				customElements.whenDefined('pr360-questionnaire').then(bootstrapPulse).catch(() => {
					bootstrapPulse();
				});
			} else {
				bootstrapPulse();
			}
		});
	})();
	</script>

<?php
if (function_exists('wp_footer')) {
	wp_footer();
}
?>

</body>

</html>