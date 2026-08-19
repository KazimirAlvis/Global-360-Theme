   <?php

    $clinic_view = global360_theme_clinic(get_the_ID());
    $addresses = $clinic_view['addresses'] ?? array();
    if (! empty($addresses)) {
        echo '<ul class="clinic-addresses">';
        foreach ($addresses as $addr) {
            $icon = global_360_get_icon_svg('location-pin', 'clinic-address-icon');
            printf(
                '<li>%s %s %s %s %s</li>',
                $icon, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                esc_html($addr['street']),
                esc_html($addr['city']),
                esc_html($addr['state']),
                esc_html($addr['zip'])
            );
        }
        echo '</ul>';
    }

    ?>
