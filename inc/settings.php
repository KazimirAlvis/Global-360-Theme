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
            add_action('admin_enqueue_scripts',     [$this, 'enqueue_color_picker']);
            add_action('wp_head',                   [$this, 'print_global_css_variables']);
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
                            
                            // Output header and footer fields
                            $this->field_header_logo(['label_for' => 'header_logo_id']);

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
         * Print CSS variables for colors/fonts (in <head>).
         */
        public function print_global_css_variables()
        {
            $opts = get_option(self::OPTION_KEY, []);
            echo '<style id="360-global-vars">:root {';
            if (! empty($opts['primary_color'])) {
                echo '--cpt360-primary: ' . esc_html($opts['primary_color']) . ';';
            }
            if (! empty($opts['secondary_color'])) {
                echo '--cpt360--preset--color--secondary: ' . esc_html($opts['secondary_color']) . ';';
            }
            if (! empty($opts['body_font'])) {
                echo '--wp--preset--font-family--body-font: var(--wp--preset--font-family--'
                    . esc_js($opts['body_font']) . ');';
            }
            if (! empty($opts['heading_font'])) {
                echo '--wp--preset--font-family--heading-font: var(--wp--preset--font-family--'
                    . esc_js($opts['heading_font']) . ');';
            }
            echo '}</style>';
        }
    }

    // Instantiate
    new _360_Global_Settings();
}
