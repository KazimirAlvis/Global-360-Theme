<?php

/**
 * Global Settings: colors, fonts & global Assessment ID
 */

if (! class_exists('_360_Global_Settings')) {

    class _360_Global_Settings
    {
        const OPTION_KEY = '360_global_settings';

        public function __construct()
        {
            add_action('admin_menu',                [$this, 'add_admin_page']);
            add_action('admin_init',                [$this, 'register_settings']);
            add_action('admin_init',                [$this, 'handle_import_export']);
            add_action('admin_enqueue_scripts',     [$this, 'enqueue_color_picker']);
            add_action('wp_head',                   [$this, 'print_global_css_variables']);
            
            // Add 360 settings to WordPress export/import
            add_action('export_wp',                 [$this, 'export_360_settings_to_xml']);
            add_action('import_end',                [$this, 'import_360_settings_from_xml']);
            add_filter('wp_import_post_data_processed', [$this, 'process_360_settings_import'], 10, 2);
            
            // Alternative approach: Hook into the WordPress importer to parse custom fields
            add_action('wp_import_insert_post',     [$this, 'parse_360_settings_from_xml'], 10, 4);
            
            // Cleanup temporary export posts
            add_action('export_wp_finish',          [$this, 'cleanup_temp_export_post']);
        }

        /**
         * Add top-level menu under “360 Settings”
         */
        public function add_admin_page()
        {
            add_menu_page(
                __('360 Settings', 'cpt360'),    // page title
                __('360 Settings', 'cpt360'),    // menu title
                'manage_options',                  // capability
                '360-settings',                    // menu slug
                [$this, 'render_settings_page'], // callback
                'dashicons-admin-generic',         // icon
                60                                 // position
            );
            
            // Add Export/Import submenu
            add_submenu_page(
                '360-settings',                    // parent slug
                __('Import/Export Settings', 'cpt360'), // page title
                __('Import/Export', 'cpt360'),     // menu title
                'manage_options',                  // capability
                '360-import-export',               // menu slug
                [$this, 'render_import_export_page'] // callback
            );
        }

        /**
         * Register our option group, section, and fields.
         */
        public function register_settings()
        {
            register_setting(
                '360_settings_group',            // option_group
                self::OPTION_KEY,                // option_name (array)
                [
                    'type'              => 'array',
                    'sanitize_callback' => [$this, 'sanitize'],
                    'default'           => [],
                ]
            );

            add_settings_section(
                '360_main_section',
                __('Global Colors & Fonts', 'cpt360'),
                '__return_false',
                '360-settings'
            );

            // Primary Color
            add_settings_field(
                'primary_color',
                __('Primary Color', 'cpt360'),
                [$this, 'field_color_picker'],
                '360-settings',
                '360_main_section',
                ['label_for' => 'primary_color']
            );

            // Secondary Color
            add_settings_field(
                'secondary_color',
                __('Secondary Color', 'cpt360'),
                [$this, 'field_color_picker'],
                '360-settings',
                '360_main_section',
                ['label_for' => 'secondary_color']
            );

            // Body Font
            add_settings_field(
                'body_font',
                __('Body Font', 'cpt360'),
                [$this, 'field_font_select'],
                '360-settings',
                '360_main_section',
                ['label_for' => 'body_font']
            );

            // Heading Font
            add_settings_field(
                'heading_font',
                __('Heading Font', 'cpt360'),
                [$this, 'field_font_select'],
                '360-settings',
                '360_main_section',
                ['label_for' => 'heading_font']
            );

            // Add a separate section for Assessment ID
            add_settings_section(
                '360_assessment_section',
                __('Global Assessment ID', 'cpt360'),
                '__return_false',
                '360-settings'
            );
            add_settings_field(
                'assessment_id',
                __('Global Assessment ID', 'cpt360'),
                [$this, 'field_assessment_id'],
                '360-settings',
                '360_assessment_section',
                ['label_for' => 'assessment_id']
            );

            // Header Logo
            add_settings_field(
                'header_logo_id',
                __('Header Logo', 'cpt360'),
                [$this, 'field_header_logo'],
                '360-settings',
                '360_main_section',
                ['label_for' => 'header_logo_id']
            );
        }

        /**
         * Enqueue WP color-picker on our settings page.
         */
        public function enqueue_color_picker($hook)
        {
            if ($hook !== 'toplevel_page_360-settings') {
                return;
            }
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
        }

        /**
         * Render a color-picker input.
         */
        public function field_color_picker($args)
        {
            $opts = get_option(self::OPTION_KEY, []);
            $val = $opts[$args['label_for']] ?? '';
            printf(
                '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="color-field" data-default-color="%3$s" />',
                esc_attr($args['label_for']),
                esc_attr(self::OPTION_KEY),
                esc_attr($val)
            );
            echo '<script>jQuery(function($){$(".color-field").wpColorPicker();});</script>';
        }

        /**
         * Render a font-select dropdown.
         */
        public function field_font_select($args)
        {
            $fonts = [
                'system-font'  => 'System Font',
                'anton'        => 'Anton',
                'arvo'         => 'Arvo',
                'bodoni-moda'  => 'Bodoni Moda',
                'cabin'        => 'Cabin',
                'chivo'        => 'Chivo',
                'roboto'       => 'Roboto',         // Add this
                'marcellus'    => 'Marcellus',      // Add this
                'inter'        => 'Inter',          // Add this
            ];
            $opts = get_option(self::OPTION_KEY, []);
            $sel = $opts[$args['label_for']] ?? '';
            echo '<select id="' . esc_attr($args['label_for']) . '" name="' . esc_attr(self::OPTION_KEY . '[' . $args['label_for'] . ']') . '">';
            foreach ($fonts as $slug => $label) {
                printf(
                    '<option value="%1$s"%2$s>%3$s</option>',
                    esc_attr($slug),
                    selected($sel, $slug, false),
                    esc_html($label)
                );
            }
            echo '</select>';
        }

        /**
         * Render the Global Assessment ID input.
         */
        public function field_assessment_id($args)
        {
            $opts = get_option(self::OPTION_KEY, []);
            $val  = $opts[$args['label_for']] ?? '';
            printf(
                '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" />',
                esc_attr($args['label_for']),
                esc_attr(self::OPTION_KEY),
                esc_attr($val)
            );
            echo '<p class="description">'
                . esc_html__('Used when a clinic has no custom Assessment ID.', 'cpt360')
                . '</p>';
        }

        /**
         * Render the Site Name input.
         */
        public function field_site_name($args)
        {
            $opts = get_option(self::OPTION_KEY, []);
            $val  = $opts[$args['label_for']] ?? '';
            printf(
                '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" placeholder="%4$s" />',
                esc_attr($args['label_for']),
                esc_attr(self::OPTION_KEY),
                esc_attr($val),
                esc_attr(get_bloginfo('name'))
            );
        }

        /**
         * Render the Google Maps API Key input.
         */
        public function field_google_maps_api($args)
        {
            $opts = get_option(self::OPTION_KEY, []);
            $val  = $opts[$args['label_for']] ?? '';
            printf(
                '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" placeholder="Enter your Google Maps API key" />',
                esc_attr($args['label_for']),
                esc_attr(self::OPTION_KEY),
                esc_attr($val)
            );
            echo '<p class="description">'
                . esc_html__('Required for map functionality and clinic geocoding. Get your API key from the Google Cloud Console.', 'cpt360')
                . '</p>';
        }

        /**
         * Render the Google Places API Key input.
         */
        public function field_google_places_api($args)
        {
            $opts = get_option(self::OPTION_KEY, []);
            $val  = $opts[$args['label_for']] ?? '';
            printf(
                '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" placeholder="Enter your Google Places API key" />',
                esc_attr($args['label_for']),
                esc_attr(self::OPTION_KEY),
                esc_attr($val)
            );
            echo '<p class="description">'
                . esc_html__('Required for automatic Google reviews functionality. Can use the same key as Maps API if Places API is enabled.', 'cpt360')
                . '</p>';
        }

        /**
         * Render the Contact Email input.
         */
        public function field_contact_email($args)
        {
            $opts = get_option(self::OPTION_KEY, []);
            $val  = $opts[$args['label_for']] ?? '';
            printf(
                '<input type="email" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" placeholder="info@example.com" />',
                esc_attr($args['label_for']),
                esc_attr(self::OPTION_KEY),
                esc_attr($val)
            );
            echo '<p class="description">'
                . esc_html__('Contact email address displayed in the footer.', 'cpt360')
                . '</p>';
        }

        /**
         * Render the Contact Phone input.
         */
        public function field_contact_phone($args)
        {
            $opts = get_option(self::OPTION_KEY, []);
            $val  = $opts[$args['label_for']] ?? '';
            printf(
                '<input type="tel" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" placeholder="(555) 123-4567" />',
                esc_attr($args['label_for']),
                esc_attr(self::OPTION_KEY),
                esc_attr($val)
            );
            echo '<p class="description">'
                . esc_html__('Contact phone number displayed in the footer.', 'cpt360')
                . '</p>';
        }

        /**
         * Render the Contact Email Label input.
         */
        public function field_contact_email_label($args)
        {
            $opts = get_option(self::OPTION_KEY, []);
            $val  = $opts[$args['label_for']] ?? 'Customer Support';
            printf(
                '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" placeholder="Customer Support" />',
                esc_attr($args['label_for']),
                esc_attr(self::OPTION_KEY),
                esc_attr($val)
            );
            echo '<p class="description">'
                . esc_html__('Label text displayed next to the email link (e.g., "Customer Support", "General Info", etc.).', 'cpt360')
                . '</p>';
        }

        /**
         * Render the Become a Provider URL input.
         */
        public function field_become_provider_url($args)
        {
            $opts = get_option(self::OPTION_KEY, []);
            $val  = $opts[$args['label_for']] ?? '';
            printf(
                '<input type="url" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" placeholder="https://example.com/become-a-provider" />',
                esc_attr($args['label_for']),
                esc_attr(self::OPTION_KEY),
                esc_attr($val)
            );
            echo '<p class="description">'
                . esc_html__('URL for the "Become a Provider" button displayed in the footer contact section.', 'cpt360')
                . '</p>';
        }

        /**
         * Render the Header Logo input.
         */

        public function field_header_logo($args)
        {
            $opts = get_option(self::OPTION_KEY, []);
            $logo_id = $opts[$args['label_for']] ?? '';
            $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';
?>
            <div id="header-logo-settings-container">
                <?php if ($logo_url): ?>
                    <img src="<?php echo esc_url($logo_url); ?>" style="max-width:200px; display:block; margin-bottom:10px;" />
                <?php endif; ?>
                <input type="hidden" id="header_logo_id" name="<?php echo esc_attr(self::OPTION_KEY . '[header_logo_id]'); ?>" value="<?php echo esc_attr($logo_id); ?>" />
                <button type="button" class="button" id="header_logo_button"><?php echo $logo_id ? esc_html__('Change Logo', 'cpt360') : esc_html__('Select Logo', 'cpt360'); ?></button>
                <button type="button" class="button" id="header_logo_remove" style="<?php echo $logo_id ? '' : 'display:none;'; ?>"><?php esc_html_e('Remove Logo', 'cpt360'); ?></button>
                <p class="description"><?php esc_html_e('Upload a logo for the header.', 'cpt360'); ?></p>
            </div>
        <?php
        }


        /**
         * Sanitize all settings inputs.
         *
         * @param array $input
         * @return array
         */
        public function sanitize($input)
        {
            $output = [];

            // Colors
            if (preg_match('/^#[0-9A-Fa-f]{6}$/', $input['primary_color'] ?? '')) {
                $output['primary_color'] = $input['primary_color'];
            }
            if (preg_match('/^#[0-9A-Fa-f]{6}$/', $input['secondary_color'] ?? '')) {
                $output['secondary_color'] = $input['secondary_color'];
            }

            // Fonts
            if (isset($input['body_font'])) {
                $output['body_font'] = sanitize_text_field($input['body_font']);
            }
            if (isset($input['heading_font'])) {
                $output['heading_font'] = sanitize_text_field($input['heading_font']);
            }

            // Global Assessment ID
            if (isset($input['assessment_id'])) {
                $output['assessment_id'] = sanitize_text_field($input['assessment_id']);
            }
            
            // Site Name
            if (isset($input['site_name'])) {
                $output['site_name'] = sanitize_text_field($input['site_name']);
            }
            
            // Google Maps API Key
            if (isset($input['google_maps_api_key'])) {
                $output['google_maps_api_key'] = sanitize_text_field($input['google_maps_api_key']);
            }

            // Google Places API Key
            if (isset($input['google_places_api_key'])) {
                $output['google_places_api_key'] = sanitize_text_field($input['google_places_api_key']);
            }

            // Contact Email
            if (isset($input['contact_email'])) {
                $output['contact_email'] = sanitize_email($input['contact_email']);
            }

            // Contact Phone
            if (isset($input['contact_phone'])) {
                $output['contact_phone'] = sanitize_text_field($input['contact_phone']);
            }

            // Contact Email Label
            if (isset($input['contact_email_label'])) {
                $output['contact_email_label'] = sanitize_text_field($input['contact_email_label']);
            }

            // Become a Provider URL
            if (isset($input['become_provider_url'])) {
                $output['become_provider_url'] = esc_url_raw($input['become_provider_url']);
            }
            
            if (isset($input['header_logo_id']) && is_numeric($input['header_logo_id'])) {
                $output['header_logo_id'] = intval($input['header_logo_id']);
            }

            // Social links
            if (isset($input['social_links']) && is_array($input['social_links'])) {
                $output['social_links'] = [];
                foreach ($input['social_links'] as $row) {
                    $platform = isset($row['platform']) ? sanitize_text_field($row['platform']) : '';
                    $url = isset($row['url']) ? esc_url_raw($row['url']) : '';
                    // Save row if either field is filled
                    if ($platform || $url) {
                        $output['social_links'][] = [
                            'platform' => $platform,
                            'url' => $url
                        ];
                    }
                }
            }

            return $output;
        }

        /**
         * Render the settings page HTML.
         */
        public function render_settings_page()
        {
            if (! current_user_can('manage_options')) {
                wp_die(__('Permission denied', 'cpt360'));
            }
        ?>
            <div class="wrap">
                <h1><?php esc_html_e('360 Global Settings', 'cpt360'); ?></h1>
                <div id="cpt360-settings-tabs">
                    <nav class="nav-tab-wrapper">
                        <a href="#" class="nav-tab nav-tab-active" data-tab="fonts-colors">Fonts & Colors</a>
                        <a href="#" class="nav-tab" data-tab="header-footer">Header & Footer</a>
                        <a href="#" class="nav-tab" data-tab="apis">APIs</a>
                        <a href="#" class="nav-tab" data-tab="assessment">Assessment ID</a>
                    </nav>
                    <form method="post" action="options.php">
                        <?php settings_fields('360_settings_group'); ?>
                        <div id="tab-fonts-colors" class="cpt360-settings-tab" style="display:block;">
                            <div class="cpt360-settings-fields-stack">
                                <?php
                                // Only output color and font fields, stacked
                                echo '<div><label for="primary_color"><strong>Primary Color</strong></label>';
                                $this->field_color_picker(['label_for' => 'primary_color']);
                                echo '<p class="description">Main accent color for your site.</p></div>';

                                echo '<div><label for="secondary_color"><strong>Secondary Color</strong></label>';
                                $this->field_color_picker(['label_for' => 'secondary_color']);
                                echo '<p class="description">Secondary accent color for your site.</p></div>';
                                ?>
                                <div class="cpt360-font-fields">
                                    <?php
                                    echo '<div><label for="body_font"><strong>Body Font</strong></label>';
                                    $this->field_font_select(['label_for' => 'body_font']);
                                    echo '<p class="description">Font used for regular text and paragraphs.</p></div>';

                                    echo '<div><label for="heading_font"><strong>Heading Font</strong></label>';
                                    $this->field_font_select(['label_for' => 'heading_font']);
                                    echo '<p class="description">Font used for headings (h1–h6).</p></div>';
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div id="tab-header-footer" class="cpt360-settings-tab" style="display:none;">
                            <?php
                            // Site Name field
                            echo '<div style="margin-bottom: 20px;">';
                            echo '<h3>Site Information</h3>';
                            echo '<div><label for="site_name"><strong>Site Name (for footer copyright)</strong></label>';
                            $this->field_site_name(['label_for' => 'site_name']);
                            echo '<p class="description">Name to display in footer copyright. Defaults to site title if empty.</p></div>';
                            echo '</div>';
                            
                            // Contact Information Section
                            echo '<div style="margin-bottom: 30px;">';
                            echo '<h3>Contact Information</h3>';
                            echo '<div style="margin-bottom: 15px;"><label for="contact_email"><strong>Contact Email</strong></label>';
                            $this->field_contact_email(['label_for' => 'contact_email']);
                            echo '</div>';
                            echo '<div style="margin-bottom: 15px;"><label for="contact_email_label"><strong>Email Label</strong></label>';
                            $this->field_contact_email_label(['label_for' => 'contact_email_label']);
                            echo '</div>';
                            echo '<div style="margin-bottom: 15px;"><label for="contact_phone"><strong>Contact Phone</strong></label>';
                            $this->field_contact_phone(['label_for' => 'contact_phone']);
                            echo '</div>';
                            echo '<div><label for="become_provider_url"><strong>Become a Provider Button URL</strong></label>';
                            $this->field_become_provider_url(['label_for' => 'become_provider_url']);
                            echo '</div>';
                            echo '</div>';
                            
                            // Header Logo Section
                            echo '<div style="margin-bottom: 30px;">';
                            echo '<h3>Header Logo</h3>';
                            $this->field_header_logo(['label_for' => 'header_logo_id']);
                            echo '</div>';

                            // Social Media Repeater
                            echo '<div id="cpt360-social-repeater"><h3>Social Media Links</h3>';
                            $opts = get_option(self::OPTION_KEY, []);
                            $social_links = isset($opts['social_links']) && is_array($opts['social_links']) ? $opts['social_links'] : [];
                            $platforms = [
                                'facebook' => 'Facebook',
                                'instagram' => 'Instagram',
                                'x' => 'X (Twitter)',
                                'youtube' => 'YouTube',
                                'tiktok' => 'TikTok',
                                'linkedin' => 'LinkedIn',
                                'website' => 'Website',
                            ];
                            echo '<div id="cpt360-social-list">';
                            if (!empty($social_links)) {
                                foreach ($social_links as $i => $row) {
                                    $platform = isset($row['platform']) ? $row['platform'] : '';
                                    $url = isset($row['url']) ? $row['url'] : '';
                                    echo '<div class="cpt360-social-row" style="margin-bottom:10px;">';
                                    echo '<select name="360_global_settings[social_links][' . $i . '][platform]" style="margin-right:10px;">';
                                    foreach ($platforms as $slug => $label) {
                                        echo '<option value="' . esc_attr($slug) . '"' . selected($platform, $slug, false) . '>' . esc_html($label) . '</option>';
                                    }
                                    echo '</select>';
                                    echo '<input type="url" name="360_global_settings[social_links][' . $i . '][url]" value="' . esc_attr($url) . '" placeholder="URL" style="width:250px; margin-right:10px;" />';
                                    echo '<button type="button" class="button cpt360-social-remove">Remove</button>';
                                    echo '</div>';
                                }
                            }
                            echo '</div>';
                            echo '<button type="button" class="button" id="cpt360-social-add">Add Social Link</button>';
                            echo '<p class="description">Add your social media links. The correct Font Awesome icon will be shown for each platform.</p>';
                            echo '</div>';
                            ?>
                        </div>
                        <div id="tab-apis" class="cpt360-settings-tab" style="display:none;">
                            <h2><?php esc_html_e('Google Maps API (Client-side)', 'cpt360'); ?></h2>
                            <div style="margin-bottom: 30px;">
                                <?php $this->field_google_maps_api(['label_for' => 'google_maps_api_key']); ?>
                            </div>

                            <h2><?php esc_html_e('Google Places API (Server-side)', 'cpt360'); ?></h2>
                            <div style="margin-bottom: 30px;">
                                <?php $this->field_google_places_api(['label_for' => 'google_places_api_key']); ?>
                                <p class="description"><?php esc_html_e('Use a separate key locked to your server IP for Place Details requests that power Google Reviews.', 'cpt360'); ?></p>
                            </div>

                            <hr style="margin: 30px 0;" />

                            <div class="cpt360-api-info">
                                <h3><?php esc_html_e('API Setup Instructions', 'cpt360'); ?></h3>
                                <ol style="line-height: 1.6;">
                                    <li><?php esc_html_e('Visit the Google Cloud Console', 'cpt360'); ?> (<a href="https://console.cloud.google.com/" target="_blank" rel="noopener">console.cloud.google.com</a>)</li>
                                    <li><?php esc_html_e('Create a new project or select an existing one', 'cpt360'); ?></li>
                                    <li><?php esc_html_e('Enable the following APIs:', 'cpt360'); ?>
                                        <ul style="margin: 10px 0; padding-left: 20px; list-style-type: disc;">
                                            <li><?php esc_html_e('Maps JavaScript API', 'cpt360'); ?></li>
                                            <li><?php esc_html_e('Geocoding API', 'cpt360'); ?></li>
                                            <li><?php esc_html_e('Maps Embed API', 'cpt360'); ?></li>
                                            <li><?php esc_html_e('Places API (including Place Details)', 'cpt360'); ?></li>
                                        </ul>
                                    </li>
                                    <li><?php esc_html_e('Create credentials (API key)', 'cpt360'); ?></li>
                                    <li><?php esc_html_e('Restrict the Maps key to your domain, and the Places key to your server IP for security', 'cpt360'); ?></li>
                                    <li><?php esc_html_e('Copy each API key into the fields above', 'cpt360'); ?></li>
                                </ol>
                                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 15px; margin-top: 20px;">
                                    <p style="margin: 0;"><strong><?php esc_html_e('Security Note:', 'cpt360'); ?></strong> <?php esc_html_e('Use separate keys for browser and server requests to keep restrictions tight and billing under control.', 'cpt360'); ?></p>
                                </div>
                                <div style="background-color: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; padding: 15px; margin-top: 15px;">
                                    <p style="margin: 0;"><strong><?php esc_html_e('Required for Maps:', 'cpt360'); ?></strong> <?php esc_html_e('Map functionality, clinic geocoding, location services, and embedded maps.', 'cpt360'); ?></p>
                                    <p style="margin: 10px 0 0 0;"><strong><?php esc_html_e('Required for Reviews:', 'cpt360'); ?></strong> <?php esc_html_e('Places Details API powers the Google reviews block on clinic pages.', 'cpt360'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div id="tab-assessment" class="cpt360-settings-tab" style="display:none;">
                            <h2><?php esc_html_e('Global Assessment ID', 'cpt360'); ?></h2>
                            <?php $this->field_assessment_id(['label_for' => 'assessment_id']); ?>
                        </div>
                        <?php submit_button(); ?>
                    </form>
                </div>
                <script>
                jQuery(function($){
                    // Tab switching
                    $('#cpt360-settings-tabs .nav-tab').on('click', function(e){
                        e.preventDefault();
                        var tab = $(this).data('tab');
                        $('#cpt360-settings-tabs .nav-tab').removeClass('nav-tab-active');
                        $(this).addClass('nav-tab-active');
                        $('.cpt360-settings-tab').hide();
                        $('#tab-' + tab).show();
                    });

                    // Social repeater add/remove
                    $('#cpt360-social-add').on('click', function(){
                        var i = $('#cpt360-social-list .cpt360-social-row').length;
                        var platforms = {
                            facebook: 'Facebook',
                            instagram: 'Instagram',
                            x: 'X (Twitter)',
                            youtube: 'YouTube',
                            tiktok: 'TikTok',
                            linkedin: 'LinkedIn',
                            website: 'Website'
                        };
                        var select = '<select name="360_global_settings[social_links]['+i+'][platform]" style="margin-right:10px;">';
                        $.each(platforms, function(slug, label){
                            select += '<option value="'+slug+'">'+label+'</option>';
                        });
                        select += '</select>';
                        var row = '<div class="cpt360-social-row" style="margin-bottom:10px;">'+select+'<input type="url" name="360_global_settings[social_links]['+i+'][url]" placeholder="URL" style="width:250px; margin-right:10px;" /><button type="button" class="button cpt360-social-remove">Remove</button></div>';
                        $('#cpt360-social-list').append(row);
                    });
                    $(document).on('click', '.cpt360-social-remove', function(){
                        $(this).closest('.cpt360-social-row').remove();
                    });
                });
                </script>
                <style>
                .nav-tab-wrapper { margin-bottom: 1em; }
                .cpt360-settings-tab { margin-top: 1em; }
                .cpt360-settings-fields-stack {
                    display: flex;
                    flex-direction: column;
                    gap: 1.5em;
                    max-width: 400px;
                    label{
                        margin-right: 20px;
                    }
                }
                .cpt360-font-fields {
                    display: flex;
                    flex-direction: column;
                    gap: 1em;
                }
                </style>
            </div>
<?php
        }

        /**
         * Get Google Maps API Key
         */
        public static function get_google_maps_api_key()
        {
            $opts = get_option(self::OPTION_KEY, []);
            return isset($opts['google_maps_api_key']) && !empty($opts['google_maps_api_key']) 
                ? $opts['google_maps_api_key'] 
                : '';
        }

        /**
         * Print CSS variables for colors/fonts (in <head>).
         */
        public function print_global_css_variables()
        {
            $opts = get_option(self::OPTION_KEY, []);
            echo '<style id="360-global-vars">:root {';
            
            // Colors
            if (! empty($opts['primary_color'])) {
                echo '--cpt360-primary: ' . esc_html($opts['primary_color']) . ';';
            }
            if (! empty($opts['secondary_color'])) {
                echo '--cpt360--preset--color--secondary: ' . esc_html($opts['secondary_color']) . ';';
            }
            
            // Fonts - Create both the preset variable and actual font stack
            if (! empty($opts['body_font'])) {
                $font_stack = $this->get_font_stack($opts['body_font']);
                echo '--wp--preset--font-family--body-font: ' . $font_stack . ';';
                // Also create the simplified version for SASS compatibility
                echo '--body-font: ' . $font_stack . ';';
            }
            if (! empty($opts['heading_font'])) {
                $font_stack = $this->get_font_stack($opts['heading_font']);
                echo '--wp--preset--font-family--heading-font: ' . $font_stack . ';';
                // Also create the simplified version for SASS compatibility  
                echo '--heading-font: ' . $font_stack . ';';
            }
            
            echo '}</style>';
        }

        /**
         * Get the actual font stack for a font slug
         */
        private function get_font_stack($font_slug)
        {
            $font_stacks = [
                'system-font' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
                'anton' => '"Anton", sans-serif',
                'arvo' => '"Arvo", serif',
                'bodoni-moda' => '"Bodoni Moda", serif', 
                'cabin' => '"Cabin", sans-serif',
                'chivo' => '"Chivo", sans-serif',
                'roboto' => '"Roboto", sans-serif',
                'marcellus' => '"Marcellus", serif',
                'inter' => '"Inter", sans-serif',
            ];
            
            return isset($font_stacks[$font_slug]) ? $font_stacks[$font_slug] : $font_stacks['system-font'];
        }

        /**
         * Handle import/export form submissions
         */
        public function handle_import_export()
        {
            if (!current_user_can('manage_options')) {
                return;
            }

            // Handle Export
            if (isset($_POST['export_360_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'export_360_settings')) {
                $settings = get_option(self::OPTION_KEY, []);
                $export_data = [
                    'version' => '1.0',
                    'timestamp' => current_time('timestamp'),
                    'site_url' => get_site_url(),
                    'settings' => $settings
                ];
                
                $filename = 'global-360-settings-' . date('Y-m-d-H-i-s') . '.json';
                
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: no-cache, no-store, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo json_encode($export_data, JSON_PRETTY_PRINT);
                exit;
            }

            // Handle Import
            if (isset($_POST['import_360_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'import_360_settings')) {
                if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
                    $file_content = file_get_contents($_FILES['import_file']['tmp_name']);
                    $import_data = json_decode($file_content, true);
                    
                    if ($import_data && isset($import_data['settings'])) {
                        // Sanitize imported settings
                        $sanitized_settings = $this->sanitize($import_data['settings']);
                        update_option(self::OPTION_KEY, $sanitized_settings);
                        
                        add_action('admin_notices', function() {
                            echo '<div class="notice notice-success is-dismissible"><p>360 Settings imported successfully!</p></div>';
                        });
                    } else {
                        add_action('admin_notices', function() {
                            echo '<div class="notice notice-error is-dismissible"><p>Invalid import file format.</p></div>';
                        });
                    }
                }
            }
        }

        /**
         * Render the import/export page
         */
        public function render_import_export_page()
        {
            if (!current_user_can('manage_options')) {
                wp_die(__('Permission denied', 'cpt360'));
            }
        ?>
            <div class="wrap">
                <h1><?php esc_html_e('Import/Export 360 Settings', 'cpt360'); ?></h1>
                
                <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                    <!-- Export Section -->
                    <div style="flex: 1; min-width: 300px; background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
                        <h2><?php esc_html_e('Export Settings', 'cpt360'); ?></h2>
                        <p><?php esc_html_e('Download all your 360 theme settings as a JSON file. This includes colors, fonts, logos, contact information, and all other customizations.', 'cpt360'); ?></p>
                        
                        <form method="post">
                            <?php wp_nonce_field('export_360_settings'); ?>
                            <p>
                                <input type="submit" name="export_360_settings" class="button button-primary" value="<?php esc_attr_e('Download Settings File', 'cpt360'); ?>" />
                            </p>
                        </form>
                        
                        <div style="background: #f0f6fc; border: 1px solid #c8e1ff; border-radius: 4px; padding: 15px; margin-top: 15px;">
                            <p style="margin: 0;"><strong><?php esc_html_e('Tip:', 'cpt360'); ?></strong> <?php esc_html_e('Save this file whenever you make significant changes to your theme settings. This creates a backup you can restore later.', 'cpt360'); ?></p>
                        </div>
                    </div>

                    <!-- Import Section -->
                    <div style="flex: 1; min-width: 300px; background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
                        <h2><?php esc_html_e('Import Settings', 'cpt360'); ?></h2>
                        <p><?php esc_html_e('Upload a previously exported 360 settings file to restore your theme customizations.', 'cpt360'); ?></p>
                        
                        <form method="post" enctype="multipart/form-data">
                            <?php wp_nonce_field('import_360_settings'); ?>
                            <p>
                                <label for="import_file"><?php esc_html_e('Select settings file:', 'cpt360'); ?></label><br>
                                <input type="file" name="import_file" id="import_file" accept=".json" required />
                            </p>
                            <p>
                                <input type="submit" name="import_360_settings" class="button button-secondary" value="<?php esc_attr_e('Import Settings', 'cpt360'); ?>" onclick="return confirm('<?php esc_attr_e('This will overwrite your current settings. Are you sure?', 'cpt360'); ?>')" />
                            </p>
                        </form>
                        
                        <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 15px; margin-top: 15px;">
                            <p style="margin: 0;"><strong><?php esc_html_e('Warning:', 'cpt360'); ?></strong> <?php esc_html_e('Importing will replace all current 360 theme settings. Make sure to export your current settings first if you want to keep them.', 'cpt360'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Current Settings Info -->
                <div style="margin-top: 30px; background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
                    <h3><?php esc_html_e('Current Settings Overview', 'cpt360'); ?></h3>
                    <?php
                    $current_settings = get_option(self::OPTION_KEY, []);
                    if (!empty($current_settings)) {
                        echo '<p>' . esc_html__('You currently have the following customizations:', 'cpt360') . '</p>';
                        echo '<ul style="columns: 2; column-gap: 30px; list-style-type: disc; padding-left: 20px;">';
                        
                        $setting_labels = [
                            'primary_color' => 'Primary Color',
                            'secondary_color' => 'Secondary Color', 
                            'body_font' => 'Body Font',
                            'heading_font' => 'Heading Font',
                            'header_logo_id' => 'Header Logo',
                            'assessment_id' => 'Assessment ID',
                            'contact_email' => 'Contact Email',
                            'contact_phone' => 'Contact Phone',
                            'google_maps_api_key' => 'Google Maps API',
                            'social_links' => 'Social Media Links'
                        ];
                        
                        foreach ($setting_labels as $key => $label) {
                            if (!empty($current_settings[$key])) {
                                if ($key === 'social_links' && is_array($current_settings[$key])) {
                                    $count = count($current_settings[$key]);
                                    echo '<li>' . esc_html($label . ' (' . $count . ' links)') . '</li>';
                                } else {
                                    echo '<li>' . esc_html($label) . '</li>';
                                }
                            }
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>' . esc_html__('No custom settings found. Configure your theme settings first, then export them for backup.', 'cpt360') . '</p>';
                    }
                    ?>
                </div>
            </div>
        <?php
        }

        /**
         * Add 360 settings to WordPress XML export
         * This will include the settings in the standard WordPress export file
         */
        public function export_360_settings_to_xml()
        {
            $settings = get_option(self::OPTION_KEY, []);
            if (!empty($settings)) {
                // Create a temporary post with our settings
                // This is more reliable than XML comments
                $temp_post_id = wp_insert_post([
                    'post_title' => '360_THEME_SETTINGS_EXPORT',
                    'post_content' => json_encode($settings, JSON_PRETTY_PRINT),
                    'post_status' => 'private',
                    'post_type' => 'post',
                    'post_date' => current_time('mysql'),
                    'meta_input' => [
                        '_360_settings_export' => 'true',
                        '_360_export_version' => '1.0'
                    ]
                ]);
                
                // Also add XML comments for backup
                echo "\n" . '<!-- 360 Global Theme Settings -->' . "\n";
                echo '<wp:360_global_settings>' . "\n";
                echo '<![CDATA[' . json_encode($settings, JSON_PRETTY_PRINT) . ']]>' . "\n";
                echo '</wp:360_global_settings>' . "\n";
                echo '<!-- End 360 Global Theme Settings -->' . "\n";
                
                // Store the temp post ID so we can clean it up later
                update_option('_360_temp_export_post_id', $temp_post_id);
            }
        }

        /**
         * Import 360 settings from WordPress XML import
         * This will restore the settings when importing a WordPress XML file
         */
        public function import_360_settings_from_xml()
        {
            // This hook runs after WordPress import is complete
            // We need to check if there are any 360 settings in the imported data
            
            // Get the uploaded XML content (if available in global scope)
            if (isset($GLOBALS['wp_import']) && method_exists($GLOBALS['wp_import'], 'get_imported_data')) {
                // Custom extraction logic would go here
                // For now, we'll add a simple admin notice to remind users about settings
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-info is-dismissible">';
                    echo '<p><strong>WordPress Import Complete!</strong> ';
                    echo 'If you exported 360 theme settings, you may need to manually import them via ';
                    echo '<a href="' . admin_url('admin.php?page=360-import-export') . '">360 Settings → Import/Export</a>.';
                    echo '</p></div>';
                });
            }
        }

        /**
         * Process 360 settings during WordPress XML import
         * This attempts to extract and import 360 settings from XML comments
         */
        public function process_360_settings_import($post_data, $raw_data)
        {
            // Look for 360 settings in the raw XML data
            if (isset($raw_data['360_global_settings'])) {
                $settings_json = $raw_data['360_global_settings'];
                $settings = json_decode($settings_json, true);
                
                if ($settings && is_array($settings)) {
                    // Sanitize and import the settings
                    $sanitized_settings = $this->sanitize($settings);
                    update_option(self::OPTION_KEY, $sanitized_settings);
                    
                    // Add success notice
                    add_action('admin_notices', function() {
                        echo '<div class="notice notice-success is-dismissible">';
                        echo '<p><strong>360 Theme Settings Imported Successfully!</strong> Your theme customizations have been restored.</p>';
                        echo '</div>';
                    });
                }
            }
            
            return $post_data;
        }

        /**
         * Parse 360 settings from WordPress XML during import
         * Alternative method that looks for our custom XML elements
         */
        public function parse_360_settings_from_xml($post_id, $original_post_ID, $postdata, $post)
        {
            // This is a fallback method - we store settings info as a special post
            // and then extract it during import. This is a more reliable method
            // than trying to parse XML comments directly.
            
            if (isset($postdata['post_title']) && $postdata['post_title'] === '360_THEME_SETTINGS_EXPORT') {
                if (isset($postdata['post_content'])) {
                    $settings = json_decode($postdata['post_content'], true);
                    if ($settings && is_array($settings)) {
                        $sanitized_settings = $this->sanitize($settings);
                        update_option(self::OPTION_KEY, $sanitized_settings);
                        
                        // Delete the temporary post since we don't need it
                        wp_delete_post($post_id, true);
                        
                        // Add success notice
                        add_action('admin_notices', function() {
                            echo '<div class="notice notice-success is-dismissible">';
                            echo '<p><strong>360 Theme Settings Imported Successfully!</strong> Your theme customizations have been restored from the WordPress export.</p>';
                            echo '</div>';
                        });
                    }
                }
            }
        }

        /**
         * Clean up temporary export posts after export is complete
         */
        public function cleanup_temp_export_post()
        {
            $temp_post_id = get_option('_360_temp_export_post_id');
            if ($temp_post_id) {
                wp_delete_post($temp_post_id, true);
                delete_option('_360_temp_export_post_id');
            }
        }
    }

    // Instantiate
    new _360_Global_Settings();
}
