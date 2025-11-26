   <?php

    $addresses = get_post_meta(get_the_ID(), 'clinic_addresses', true);
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